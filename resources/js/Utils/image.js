/**
 * Formats a database path into a valid public URL.
 * 
 * @param {string} path - The image path from the database.
 * @param {string} fallback - The fallback image URL if path is null.
 * @returns {string} - The corrected public URL.
 */
export const getImageUrl = (path, fallback = "/assets/default.png") => {
    if (!path) return fallback;

    // If it's already a full URL or an asset path, return as is
    if (path.startsWith("http") || path.startsWith("/assets") || path.startsWith("/storage")) {
        return path;
    }

    // Ensure relative paths are prefixed with /storage/
    const cleanPath = path.startsWith("/") ? path.substring(1) : path;
    return `/storage/${cleanPath}`;
};
