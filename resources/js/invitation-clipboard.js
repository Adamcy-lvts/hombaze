/**
 * HomeBaze Invitation Clipboard Utilities
 */

// Copy invitation link to clipboard
window.copyInvitationLink = function(link) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(link).then(() => {
            showCopySuccess('Invitation link copied to clipboard!');
        }).catch(err => {
            fallbackCopyTextToClipboard(link);
        });
    } else {
        fallbackCopyTextToClipboard(link);
    }
};

// Copy WhatsApp message to clipboard
window.copyWhatsAppMessage = function(message) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(message).then(() => {
            showCopySuccess('WhatsApp message copied to clipboard!');
        }).catch(err => {
            fallbackCopyTextToClipboard(message);
        });
    } else {
        fallbackCopyTextToClipboard(message);
    }
};

// Copy SMS message to clipboard
window.copySmsMessage = function(message) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(message).then(() => {
            showCopySuccess('SMS message copied to clipboard!');
        }).catch(err => {
            fallbackCopyTextToClipboard(message);
        });
    } else {
        fallbackCopyTextToClipboard(message);
    }
};

// Fallback copy method for older browsers
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;

    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess('Copied to clipboard!');
        } else {
            showCopyError('Failed to copy to clipboard');
        }
    } catch (err) {
        showCopyError('Failed to copy to clipboard');
    }

    document.body.removeChild(textArea);
}

// Show success notification
function showCopySuccess(message) {
    showNotification(message, 'success');
}

// Show error notification
function showCopyError(message) {
    showNotification(message, 'error');
}

// Generic notification function
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;

    if (type === 'success') {
        notification.className += ' bg-green-500 text-white';
    } else {
        notification.className += ' bg-red-500 text-white';
    }

    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success'
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                }
            </svg>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Generate WhatsApp share URL
window.generateWhatsAppUrl = function(phone, message) {
    const encodedMessage = encodeURIComponent(message);
    const cleanPhone = phone.replace(/[^\d+]/g, '');
    return `https://wa.me/${cleanPhone}?text=${encodedMessage}`;
};

// Open WhatsApp with pre-filled message
window.shareViaWhatsApp = function(phone, message) {
    const url = generateWhatsAppUrl(phone, message);
    window.open(url, '_blank');
};

// Track invitation sharing
window.trackInvitationShare = function(invitationId, method) {
    fetch('/api/track-invitation-share', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({
            invitation_id: invitationId,
            method: method
        })
    }).catch(err => {
        console.log('Tracking request failed:', err);
    });
};