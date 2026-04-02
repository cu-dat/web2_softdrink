<?php /* FOOTER MINI FULL - COMPACT */ ?>

<style>
/* ===== FOOTER ===== */
.footer-mini{
    background: #000;
    border-top: 1px solid #333;
    font-size: 13px; /* 🔥 nhỏ lại cho gọn */
}

/* TITLE */
.footer-mini h6{
    color: #ffc107;
    margin-bottom: 8px; /* 🔥 giảm */
}

/* TEXT */
.footer-mini p{
    margin: 4px 0; /* 🔥 giảm */
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

/* ===== BRAND ===== */
.footer-brand{
    display: flex;
    align-items: center;
    gap: 8px; /* 🔥 giảm */
    margin-bottom: 5px; /* 🔥 giảm */
}

.footer-logo-left{
    height: 35px; /* 🔥 nhỏ lại */
    object-fit: contain;
}

.footer-brand h6{
    margin: 0;
    color: #ffc107;
    font-weight: bold;
}

/* ===== ICON ===== */
.footer-icon{
    width: 28px;  /* 🔥 nhỏ lại */
    height: 28px;
    background: #d4b06a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000;
    font-size: 13px;
}

.footer-item{
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px; /* 🔥 giảm */
    color: #ccc;
}

/* ===== LOGO CENTER ===== */
.footer-logo-mini{
    height: 28px; /* 🔥 nhỏ lại */
    margin-top: 5px; /* 🔥 giảm */
}

/* ===== FULL WIDTH + COMPACT ===== */
.footer-mini .container,
.footer-mini .container-fluid{
    padding-top: 15px;   /* 🔥 giảm */
    padding-bottom: 8px; /* 🔥 giảm */
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

```
<!-- FULL WIDTH giống header -->
<div class="container-fluid px-5">
    <div class="row">

        <!-- CỘT 1 -->
        <div class="col-md-4 mb-2">
            <div class="footer-brand">
                <img src="/web2_softdrink/img/logo.jpg" class="footer-logo-left">
                <h6>Bobiboo</h6>
            </div>
            <p>Uy tín tạo nên thương hiệu</p>
        </div>

        <!-- CỘT 2 -->
        <div class="col-md-4 mb-2">
            <h6>DỊCH VỤ</h6>
            <p><a>Sản phẩm</a></p>
            <p><a>Chính sách</a></p>
            <p><a>Thanh toán</a></p>
        </div>

        <!-- CỘT 3 -->
        <div class="col-md-4 mb-2">
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

    <!-- LOGO + COPYRIGHT -->
    <div class="text-center">
        <img src="/web2_softdrink/img/logo.jpg" class="footer-logo-mini">
    </div>

    <div class="text-center text-secondary small mt-1">
        © 2026 Soft Drink Store
    </div>

</div>
```

</footer>
