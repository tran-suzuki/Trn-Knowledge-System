export const formatYmd = (s: string): string => {
	if (!s) return '';
	// DB "YYYY-MM-DD HH:mm:ss" -> "YYYY/MM/DD"
	if (s.includes(' ')) return s.slice(0, 10).replaceAll('-', '/');

	// ISO -> "YYYY/MM/DD"
	const d = new Date(s);
	if (Number.isNaN(d.getTime())) return '';
	const y = d.getFullYear();
	const m = String(d.getMonth() + 1).padStart(2, '0');
	const day = String(d.getDate()).padStart(2, '0');
	return `${y}/${m}/${day}`;
};
