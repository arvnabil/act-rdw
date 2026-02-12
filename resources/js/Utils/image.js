/**
 * Formats a database path into a valid public URL.
 * 
 * @param {string} path - The image path from the database.
 * @returns {string} - The corrected public URL.
 */
export const getImageUrl = (path) => {
    if (!path) return "";

    // If it's already a full URL or an asset path, return as is
    if (path.startsWith("http") || path.startsWith("/assets") || path.startsWith("/storage")) {
        return path;
    }

    // Ensure relative paths are prefixed with /storage/
    // Filament stores paths relative to the 'public' disk root (storage/app/public)
    const cleanPath = path.startsWith("/") ? path.substring(1) : path;
    return `/storage/${cleanPath}`;
};
