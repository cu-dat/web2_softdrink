    <?php /* FOOTER MINI FULL */ ?>

    <style>
    /* ===== FOOTER ===== */
    .footer-mini{
        background: #000;
        border-top: 1px solid #333;
        font-size: 14px;
    }

    /* TITLE */
    .footer-mini h6{
        color: #ffc107;
        margin-bottom: 10px;
    }

    /* TEXT */
    .footer-mini p{
        margin: 6px 0;
        color: #ccc;
    }

    /* LINK */
    .footer-mini a{
        color: #ccc;
        text-decoration: none;
    }

    .footer-mini a:hover{
        color: #ffc107;
    }

    /* ===== BRAND (LOGO + TEXT) ===== */
    .footer-brand{
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }

    .footer-logo-left{
        height: 40px;
        object-fit: contain;
    }

    .footer-brand h6{
        margin: 0;
        color: #ffc107;
        font-weight: bold;
    }

    /* ===== ICON ===== */
    .footer-icon{
        width: 32px;
        height: 32px;
        background: #d4b06a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #000;
        font-size: 14px;
    }

    .footer-item{
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        color: #ccc;
    }

    /* ===== LOGO CENTER ===== */
    .footer-logo-mini{
        height: 40px;
        margin-top: 10px;
    }

    /* ===== PADDING ===== */
    .footer-mini .container{
        padding-top: 25px;
        padding-bottom: 15px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px){
        .footer-mini{
            text-align: center;
        }

        .footer-brand{
            justify-content: center;
        }

        .footer-item{
            justify-content: center;
        }
    }
    </style>

    <footer class="footer-mini">

        <div class="container">
            <div class="row">

                <!-- CỘT 1 -->
                <div class="col-md-4 mb-3">

                    <div class="footer-brand">
                        <img src="/web2_softdrink/img/logo.jpg" class="footer-logo-left">
                        <h6>Bobiboo</h6>
                    </div>

                    <p>Uy tín tạo nên thương hiệu</p>

                </div>

                <!-- CỘT 2 -->
                <div class="col-md-4 mb-3">
                    <h6>DỊCH VỤ</h6>
                    <p><a>Sản phẩm</a></p>
                    <p><a>Chính sách</a></p>
                    <p><a>Thanh toán</a></p>
                </div>

                <!-- CỘT 3 -->
                <div class="col-md-4 mb-3">
                    <h6>LIÊN HỆ</h6>

                    <div class="footer-item">
                        <div class="footer-icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <span>1900 1234</span>
                    </div>

                    <div class="footer-item">
                        <div class="footer-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <span>support@softdrink.com</span>
                    </div>

                    <div class="footer-item">
                        <div class="footer-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <span>TP.HCM</span>
                    </div>

                </div>

            </div>

            <!-- LOGO DƯỚI -->
            <div class="text-center">
                <img src="/web2_softdrink/img/logo.jpg" class="footer-logo-mini">
            </div>

            <!-- COPYRIGHT -->
            <div class="text-center text-secondary small mt-2">
                © 2026 Soft Drink Store
            </div>

        </div>

    </footer>