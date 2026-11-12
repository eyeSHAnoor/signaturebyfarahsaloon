export function formatPkr(value: string | number): string {
    const amount = typeof value === 'number' ? value : Number(value);

    return `PKR ${amount.toLocaleString('en-PK', {
        maximumFractionDigits: 0,
    })}`;
}
