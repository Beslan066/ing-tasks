export const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
