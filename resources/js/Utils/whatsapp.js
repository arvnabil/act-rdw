/**
 * Generates a WhatsApp link with a formatted number and message.
 * 
 * @param {string} whatsappNumber - The phone number from settings.
 * @param {string} message - Optional custom message.
 * @returns {string|null} - The formatted WhatsApp link or null if no number provided.
 */
export const getWhatsAppLink = (whatsappNumber, message = "Halo ACTiV, saya ingin bertanya mengenai layanan Anda.") => {
    if (!whatsappNumber) return null;

    // Clean non-digits
    let number = whatsappNumber.replace(/\D/g, "");

    // Handle Indonesian specific convenience (leading 0 to 62)
    if (number.startsWith("0")) {
        number = "62" + number.substring(1);
    }

    // Default message if empty
    const text = message || "Halo ACTiV, saya ingin bertanya mengenai layanan Anda.";

    return `https://wa.me/${number}?text=${encodeURIComponent(text)}`;
};
