/**
 * Generates a WhatsApp link that routes through the internal tracker.
 * It appends current URL parameters (UTM) and optional tracking metadata.
 * 
 * @param {string} whatsappNumber - The phone number (used for validation only).
 * @param {object} options - Tracking options.
 * @returns {string|null} - The internal tracking link or null.
 */
export const getWhatsAppLink = (whatsappNumber, options = {}) => {
    if (!whatsappNumber) return null;

    let finalNumber = whatsappNumber;
    let manualMessage = null;

    // Detect if the input is a full WhatsApp URL (backward compatibility)
    if (typeof whatsappNumber === 'string' && (whatsappNumber.includes('wa.me') || whatsappNumber.includes('api.whatsapp.com'))) {
        try {
            const url = new URL(whatsappNumber.startsWith('http') ? whatsappNumber : `https://${whatsappNumber}`);

            // Extract phone number from path (wa.me) or query (api)
            if (url.hostname === 'wa.me') {
                finalNumber = url.pathname.replace('/', '');
            } else {
                finalNumber = url.searchParams.get('phone') || finalNumber;
            }

            // Extract message
            manualMessage = url.searchParams.get('text') || url.searchParams.get('message');
        } catch (e) {
            // Fallback if parsing fails
            console.warn("Failed to parse manual WhatsApp URL:", whatsappNumber);
        }
    }

    const defaultMsg = "Halo ACTiV, saya ingin bertanya mengenai layanan Anda.";

    const opts = typeof options === 'string' ? { message: options } : options;
    const message = opts.message || manualMessage || defaultMsg;

    const {
        cta_position = 'generic',
        cta_label = null,
        entity_type = null,
        entity_id = null,
        entity_slug = null,
        ...extraParams
    } = opts;

    // Get current query params from URL (to preserve UTMs from ad campaigns)
    const params = new URLSearchParams(window.location.search);

    // Set core tracking data
    if (finalNumber) params.set('phone', finalNumber);
    params.set('text', message);
    params.set('cta_position', cta_position);
    if (cta_label) params.set('cta_label', cta_label);

    // Set entity tracking data
    if (entity_type) params.set('entity_type', entity_type);
    if (entity_id) params.set('entity_id', entity_id);
    if (entity_slug) params.set('entity_slug', entity_slug);

    // Add extra params (UTMs override if provided)
    Object.entries(extraParams).forEach(([key, value]) => {
        if (value) params.set(key, value);
    });

    // Full URL for better context
    params.set('page_route', window.location.href);

    // Return internal route
    return `/wa?${params.toString()}`;
};
