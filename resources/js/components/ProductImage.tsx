const colors: Record<string, string> = {
    R01: 'bg-danger',
    G01: 'bg-success',
    B01: 'bg-primary',
};

export default function ProductImage({ code, className = '', size }: { code: string; className?: string; size?: number }) {
    return <div className={`${colors[code] ?? 'bg-secondary'} bg-gradient ${className}`} style={size ? { width: size, height: size } : undefined} />;
}
