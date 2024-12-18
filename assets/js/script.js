// Fungsi untuk menambahkan ke keranjang
function addToCart(productId) {
    // Mencegah multiple klik
    const button = event.target;
    if (button.disabled) return;
    
    // Disable button sementara
    button.disabled = true;
    
    // Kirim request ke server
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            // Tambahkan CSRF token jika ada
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update cart count
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = data.cart_count;
            }
            // Optional: Tampilkan pesan sukses
            showNotification('Produk berhasil ditambahkan ke keranjang');
        } else {
            throw new Error(data.message || 'Gagal menambahkan ke keranjang');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Gagal menambahkan produk ke keranjang', 'error');
    })
    .finally(() => {
        // Enable button kembali setelah delay
        setTimeout(() => {
            button.disabled = false;
        }, 500);
    });
}

// Fungsi untuk menampilkan notifikasi
function showNotification(message, type = 'success') {
    // Cek apakah sudah ada notifikasi
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    // Buat elemen notifikasi baru
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;

    // Tambahkan ke DOM
    document.body.appendChild(notification);

    // Hapus notifikasi setelah 3 detik
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Fungsi untuk mencegah layout shift saat loading gambar
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
        // Jika gambar sudah di-cache
        if (img.complete) {
            img.classList.remove('loading');
        } else {
            img.addEventListener('load', function() {
                img.classList.remove('loading');
            });
            img.addEventListener('error', function() {
                img.src = 'assets/images/products/placeholder.jpg';
                img.classList.remove('loading');
            });
        }
    });
});

// Fungsi untuk mencegah double submit form
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (this.hasAttribute('data-submitted')) {
            e.preventDefault();
        } else {
            this.setAttribute('data-submitted', 'true');
        }
    });
});

// Tambahkan CSS untuk notifikasi
const style = document.createElement('style');
style.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 4px;
        color: white;
        z-index: 1000;
        animation: none;
    }
    .notification.success {
        background-color: #2ecc71;
    }
    .notification.error {
        background-color: #e74c3c;
    }
    img.loading {
        background-color: #f5f5f5;
    }
`;
document.head.appendChild(style); 