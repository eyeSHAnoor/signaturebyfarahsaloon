<?php

namespace App\Http\Controllers\Salon;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\SalonGalleryItem;
use App\Models\SalonSetting;
use App\Models\SalonTestimonial;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Stylist;
use App\Models\User;
use App\Services\Salon\AppointmentScheduler;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class SalonAdminController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('salon/Admin', [
            'stats' => [
                'appointments' => Appointment::query()->count(),
                'pending' => Appointment::query()->where('status', 'pending')->count(),
                'completed' => Appointment::query()->where('status', 'completed')->count(),
                'customers' => User::query()
                    ->whereDoesntHave('roles', fn ($query) => $query->where('name', 'super_admin'))
                    ->count(),
                'revenue' => Appointment::query()->where('status', 'completed')->sum('price'),
            ],
            'appointments' => Appointment::query()->with(['service', 'stylist'])->latest('appointment_date')->latest('start_time')->take(30)->get(),
            'categories' => ServiceCategory::query()->orderBy('sort_order')->get(),
            'services' => Service::query()->with('category')->orderBy('name')->get(),
            'stylists' => Stylist::query()->orderBy('name')->get(),
            'gallery' => SalonGalleryItem::query()->orderBy('sort_order')->get(),
            'testimonials' => SalonTestimonial::query()->latest()->get(),
            'settings' => SalonSetting::query()->pluck('value', 'key')->all(),
            'customers' => User::query()
                ->whereDoesntHave('roles', fn ($query) => $query->where('name', 'super_admin'))
                ->latest()
                ->take(100)
                ->get(['id', 'name', 'email', 'phone', 'created_at'])
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'status' => $user->status,
                    'created_at' => $user->created_at,
                ]),
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:service_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'active' => ['sometimes', 'boolean'],
        ]);

        ServiceCategory::query()->create([...$data, 'slug' => Str::slug($data['name'])]);

        return back()->with('success', 'Service category created.');
    }

    public function updateCategory(Request $request, ServiceCategory $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('service_categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'active' => ['required', 'boolean'],
        ]);

        $category->update([...$data, 'slug' => Str::slug($data['name'])]);

        return back()->with('success', 'Service category updated.');
    }

    public function destroyCategory(ServiceCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Service category removed.');
    }

    public function storeService(Request $request): RedirectResponse
    {
        $data = $this->serviceData($request);
        $data['image_url'] = $this->uploadedImageUrl($request, 'image') ?? ($data['image_url'] ?? null);
        unset($data['image']);
        $data['slug'] = $this->uniqueServiceSlug($data['name']);
        Service::query()->create($data);

        return back()->with('success', 'Service created.');
    }

    public function updateService(Request $request, Service $service): RedirectResponse
    {
        $data = $this->serviceData($request, $service);
        $data['image_url'] = $this->uploadedImageUrl($request, 'image') ?? ($data['image_url'] ?? null);
        unset($data['image']);
        $data['slug'] = $service->name === $data['name'] ? $service->slug : $this->uniqueServiceSlug($data['name'], $service);
        $service->update($data);

        return back()->with('success', 'Service updated.');
    }

    public function destroyService(Service $service): RedirectResponse
    {
        $service->update(['active' => false]);

        return back()->with('success', 'Service is no longer available for booking.');
    }

    public function storeStylist(Request $request): RedirectResponse
    {
        $data = $this->stylistData($request);
        $data['image_url'] = $this->uploadedImageUrl($request, 'image') ?? ($data['image_url'] ?? null);
        unset($data['image']);
        Stylist::query()->create($data);

        return back()->with('success', 'Stylist added.');
    }

    public function updateStylist(Request $request, Stylist $stylist): RedirectResponse
    {
        $data = $this->stylistData($request);
        $data['image_url'] = $this->uploadedImageUrl($request, 'image') ?? ($data['image_url'] ?? null);
        unset($data['image']);
        $stylist->update($data);

        return back()->with('success', 'Stylist updated.');
    }

    public function destroyStylist(Stylist $stylist): RedirectResponse
    {
        $stylist->update(['active' => false]);

        return back()->with('success', 'Stylist is no longer available for booking.');
    }

    public function updateCustomer(Request $request, User $user): RedirectResponse
    {
        abort_if($user->hasRole('super_admin'), 403);

        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'suspended', 'banned'])],
            'suspended_until' => ['nullable', 'date', 'after:now'],
        ]);

        $user->update([
            'status' => $data['status'],
            'suspended_until' => $data['status'] === 'suspended'
                ? ($data['suspended_until'] ?? now()->addDays(30))
                : null,
        ]);

        return back()->with('success', 'Customer account updated.');
    }

    public function updateAppointment(
        Request $request,
        Appointment $appointment,
        AppointmentScheduler $scheduler,
    ): RedirectResponse {
        $data = $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'approved', 'rejected', 'cancelled', 'completed'])],
            'appointment_date' => ['nullable', 'date', 'after_or_equal:today'],
            'start_time' => ['nullable', 'date_format:H:i'],
        ]);

        DB::transaction(function () use ($appointment, $data, $scheduler) {
            $appointment = Appointment::query()->whereKey($appointment->id)->lockForUpdate()->firstOrFail();
            $oldStatus = $appointment->status;
            $newStatus = $data['status'] ?? $oldStatus;
            $date = isset($data['appointment_date'])
                ? CarbonImmutable::parse($data['appointment_date'])
                : CarbonImmutable::parse($appointment->appointment_date->toDateString());
            $time = $data['start_time'] ?? substr((string) $appointment->start_time, 0, 5);
            $moving = isset($data['appointment_date']) || isset($data['start_time']);
            $oldIsReleased = in_array($oldStatus, ['rejected', 'cancelled'], true);
            $newIsReleased = in_array($newStatus, ['rejected', 'cancelled'], true);
            $needsReservation = ! $newIsReleased && ($moving || $oldIsReleased);

            if ($needsReservation) {
                if (! $appointment->stylist_id) {
                    throw ValidationException::withMessages(['start_time' => 'Assign an active stylist before scheduling this appointment.']);
                }

                $scheduler->lockStylist($appointment->stylist_id);
                $start = CarbonImmutable::createFromFormat('Y-m-d H:i', $date->toDateString().' '.$time);
                if ($start->minute % 30 !== 0 || ! in_array(
                    $time,
                    $scheduler->availableStartTimes($appointment->stylist_id, $appointment->service->duration_minutes, $date, $appointment->id),
                    true,
                )) {
                    throw ValidationException::withMessages(['start_time' => 'That stylist is not available at the selected time.']);
                }

                $appointment->reservations()->delete();
                $appointment->update([
                    'appointment_date' => $date->toDateString(),
                    'start_time' => $start->format('H:i:s'),
                    'end_time' => $start->addMinutes($appointment->service->duration_minutes)->format('H:i:s'),
                    'status' => $newStatus,
                ]);
                $scheduler->reserve($appointment->fresh());
            } else {
                if ($newIsReleased) {
                    $appointment->reservations()->delete();
                }

                if ($moving) {
                    throw ValidationException::withMessages(['start_time' => 'Cancelled appointments cannot be rescheduled. Restore the appointment first.']);
                }

                if (isset($data['status'])) {
                    $appointment->update(['status' => $newStatus]);
                }
            }
        });

        return back()->with('success', 'Appointment updated.');
    }

    public function storeGalleryItem(Request $request): RedirectResponse
    {
        $data = $this->galleryData($request);
        $this->saveGalleryImages($request, $data);
        SalonGalleryItem::query()->create($data);

        return back()->with('success', 'Gallery image added.');
    }

    public function updateGalleryItem(Request $request, SalonGalleryItem $galleryItem): RedirectResponse
    {
        $data = $this->galleryData($request);
        $this->saveGalleryImages($request, $data, $galleryItem);
        $galleryItem->update($data);

        return back()->with('success', 'Gallery image updated.');
    }

    private function galleryData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:100'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image' => ['nullable', 'image', 'max:5120'],
            'before_image_url' => ['nullable', 'url', 'max:2048'],
            'before_image' => ['nullable', 'image', 'max:5120'],
            'after_image_url' => ['nullable', 'url', 'max:2048'],
            'after_image' => ['nullable', 'image', 'max:5120'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'active' => ['sometimes', 'boolean'],
        ]);
    }

    private function saveGalleryImages(Request $request, array &$data, ?SalonGalleryItem $galleryItem = null): void
    {
        $imageUploadUrl = $this->uploadedImageUrl($request, 'image');
        $beforeUploadUrl = $this->uploadedImageUrl($request, 'before_image');
        $afterUploadUrl = $this->uploadedImageUrl($request, 'after_image');
        $imageUrl = $imageUploadUrl
            ?? (array_key_exists('image_url', $data) ? $data['image_url'] : $galleryItem?->image_url);
        $beforeImageUrl = $beforeUploadUrl
            ?? (array_key_exists('before_image_url', $data) ? $data['before_image_url'] : $galleryItem?->before_image_url);
        $afterImageUrl = $afterUploadUrl
            ?? (array_key_exists('after_image_url', $data) ? $data['after_image_url'] : $galleryItem?->after_image_url);
        if ($galleryItem && $galleryItem->image_url === $galleryItem->before_image_url && $imageUrl === $galleryItem->image_url) {
            $imageUrl = $beforeImageUrl;
        }
        $hasImage = ! empty($imageUrl) || $request->hasFile('image');
        $hasBefore = ! empty($beforeImageUrl) || $request->hasFile('before_image');
        $hasAfter = ! empty($afterImageUrl) || $request->hasFile('after_image');

        if (! $hasImage && ! $hasBefore && ! $hasAfter) {
            throw ValidationException::withMessages(['image_url' => 'Choose an image to upload or provide an image URL.']);
        }
        if ($hasBefore && ! $hasAfter) {
            throw ValidationException::withMessages(['after_image_url' => 'Add both images to create a before-and-after pair.']);
        }
        if ($hasAfter && ! $hasBefore) {
            throw ValidationException::withMessages(['before_image_url' => 'Add both images to create a before-and-after pair.']);
        }

        $data['image_url'] = $imageUrl ?? $beforeImageUrl ?? $afterImageUrl;
        $data['before_image_url'] = $beforeImageUrl;
        $data['after_image_url'] = $afterImageUrl;
        unset($data['image'], $data['before_image'], $data['after_image']);
    }

    public function destroyGalleryItem(SalonGalleryItem $galleryItem): RedirectResponse
    {
        $galleryItem->delete();

        return back()->with('success', 'Gallery image removed.');
    }

    public function storeTestimonial(Request $request): RedirectResponse
    {
        $data = $this->testimonialData($request);
        $data['image_url'] = $this->uploadedImageUrl($request, 'image') ?? ($data['image_url'] ?? null);
        unset($data['image']);
        SalonTestimonial::query()->create($data);

        return back()->with('success', 'Testimonial added.');
    }

    public function updateTestimonial(Request $request, SalonTestimonial $testimonial): RedirectResponse
    {
        $data = $this->testimonialData($request);
        $data['image_url'] = $this->uploadedImageUrl($request, 'image')
            ?? (array_key_exists('image_url', $data) ? $data['image_url'] : $testimonial->image_url);
        unset($data['image']);
        $testimonial->update($data);

        return back()->with('success', 'Testimonial updated.');
    }

    private function testimonialData(Request $request): array
    {
        return $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_title' => ['nullable', 'string', 'max:150'],
            'quote' => ['required', 'string', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image' => ['nullable', 'image', 'max:5120'],
            'active' => ['sometimes', 'boolean'],
        ]);
    }

    public function destroyTestimonial(SalonTestimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return back()->with('success', 'Testimonial removed.');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $settings = $request->validate([
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'home_heading' => ['nullable', 'string', 'max:180'],
            'home_description' => ['nullable', 'string', 'max:600'],
            'offer_title' => ['nullable', 'string', 'max:150'],
            'offer_description' => ['nullable', 'string', 'max:600'],
        ]);

        foreach ($settings as $key => $value) {
            SalonSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Salon contact settings saved.');
    }

    private function serviceData(Request $request, ?Service $service = null): array
    {
        return $request->validate([
            'service_category_id' => ['nullable', 'integer', 'exists:service_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'duration_minutes' => ['required', 'integer', 'min:30', 'max:240', 'multiple_of:30'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image' => ['nullable', 'image', 'max:5120'],
            'featured' => ['required', 'boolean'],
            'active' => ['required', 'boolean'],
        ]);
    }

    private function stylistData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'title' => ['required', 'string', 'max:150'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image' => ['nullable', 'image', 'max:5120'],
            'active' => ['required', 'boolean'],
        ]);
    }

    private function uploadedImageUrl(Request $request, string $field): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        $path = $request->file($field)->store('salon/images', 'public');
        if (! $path) {
            throw new RuntimeException('The image could not be stored.');
        }

        return Storage::disk('public')->url($path);
    }

    private function uniqueServiceSlug(string $name, ?Service $ignore = null): string
    {
        $slug = Str::slug($name);
        $query = Service::query()->where('slug', $slug);
        if ($ignore) {
            $query->whereKeyNot($ignore->id);
        }
        if ($query->exists()) {
            $slug .= '-'.Str::lower(Str::random(6));
        }

        return $slug;
    }
}
