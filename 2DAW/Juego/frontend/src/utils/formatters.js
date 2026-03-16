export function formatNumber(num) {
    if (num === null || num === undefined) return '0';
    if (num < 1000) return Math.floor(num).toString();
    
    const suffixes = ['', 'K', 'M', 'B', 'T', 'Q'];
    const suffixNum = Math.floor(("" + Math.floor(num)).length / 3);
    
    let shortValue = parseFloat((suffixNum !== 0 ? (num / Math.pow(1000, suffixNum)) : num).toPrecision(3));
    if (shortValue % 1 !== 0) {
        shortValue = shortValue.toFixed(1);
    }
    
    return shortValue + suffixes[suffixNum];
}
