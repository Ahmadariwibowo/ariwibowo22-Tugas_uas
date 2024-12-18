    </div> <!-- Tutup content-wrapper -->
    
    <footer class="footer">
        <div class="footer-top py-5 bg-dark text-light">
            <div class="container">
                <div class="row g-4">
                    <!-- Casual Store Section -->
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-widget">
                            <h4 class="mb-4">
                                <i class="bi bi-shop me-2"></i>Casual Store
                            </h4>
                            <p class="mb-4">
                                Fashion terkini untuk gaya casual Anda. 
                                Temukan koleksi pakaian berkualitas dengan harga terbaik.
                            </p>
                            <div class="social-links">
                                <a href="#" class="social-link">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class="bi bi-instagram"></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class="bi bi-twitter"></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class="bi bi-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Kategori Populer Section -->
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-widget">
                            <h5 class="mb-4">Kategori Populer</h5>
                            <ul class="footer-links">
                                <li>
                                    <a href="products.php?category=shirt">
                                        <i class="bi bi-chevron-right me-2"></i>Baju Casual
                                    </a>
                                </li>
                                <li>
                                    <a href="products.php?category=pants">
                                        <i class="bi bi-chevron-right me-2"></i>Celana Trendy
                                    </a>
                                </li>
                                <li>
                                    <a href="products.php?category=jacket">
                                        <i class="bi bi-chevron-right me-2"></i>Jaket Stylish
                                    </a>
                                </li>
                                <li>
                                    <a href="products.php">
                                        <i class="bi bi-chevron-right me-2"></i>Semua Produk
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Kontak Kami Section -->
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-widget">
                            <h5 class="mb-4">Kontak Kami</h5>
                            <div class="contact-info">
                                <p class="d-flex align-items-center mb-3">
                                    <i class="bi bi-geo-alt me-3"></i>
                                    <span>Jl. Contoh No. 123, Kota, Indonesia</span>
                                </p>
                                <p class="d-flex align-items-center mb-3">
                                    <i class="bi bi-telephone me-3"></i>
                                    <span>+62 123 4567 890</span>
                                </p>
                                <p class="d-flex align-items-center mb-3">
                                    <i class="bi bi-envelope me-3"></i>
                                    <span>info@casualstore.com</span>
                                </p>
                                <p class="d-flex align-items-center mb-0">
                                    <i class="bi bi-clock me-3"></i>
                                    <span>Senin - Minggu: 09:00 - 22:00</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <style>
    .footer {
        margin-top: auto;
    }

    .footer-top {
        background-color: #212529;
    }

    .footer-bottom {
        background-color: #1a1e21;
    }

    .footer-widget h4, 
    .footer-widget h5 {
        font-weight: 600;
        position: relative;
        padding-bottom: 10px;
    }

    .footer-widget h4::after,
    .footer-widget h5::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 2px;
        background: #0d6efd;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 10px;
    }

    .footer-links a {
        color: #adb5bd;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-links a:hover {
        color: #ffffff;
        padding-left: 5px;
    }

    .social-links {
        display: flex;
        gap: 10px;
    }

    .social-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background: #0d6efd;
        color: white;
        transform: translateY(-3px);
    }

    .contact-info p {
        color: #adb5bd;
    }

    .contact-info i {
        font-size: 1.2rem;
        color: #0d6efd;
    }

    .payment-methods i {
        font-size: 1.5rem;
        color: #adb5bd;
        transition: color 0.3s ease;
    }

    .payment-methods i:hover {
        color: #ffffff;
    }

    @media (max-width: 768px) {
        .footer-widget {
            text-align: center;
        }

        .footer-widget h4::after,
        .footer-widget h5::after {
            left: 50%;
            transform: translateX(-50%);
        }

        .social-links {
            justify-content: center;
        }

        .contact-info p {
            justify-content: center;
        }
    }
    </style>
</body>
</html> 