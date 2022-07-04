</div>
</div>
<!-- main @e -->
</div>
<!-- app-root @e -->
<!-- select region modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="region">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
            <div class="modal-body modal-body-md">
                <h5 class="title mb-4">Select Your Country</h5>
                <div class="nk-country-region">
                    <ul class="country-list text-center gy-2">
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/arg.png" alt="" class="country-flag">
                                <span class="country-name">Argentina</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/aus.png" alt="" class="country-flag">
                                <span class="country-name">Australia</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/bangladesh.png" alt="" class="country-flag">
                                <span class="country-name">Bangladesh</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/canada.png" alt="" class="country-flag">
                                <span class="country-name">Canada <small>(English)</small></span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/china.png" alt="" class="country-flag">
                                <span class="country-name">Centrafricaine</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/china.png" alt="" class="country-flag">
                                <span class="country-name">China</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/french.png" alt="" class="country-flag">
                                <span class="country-name">France</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/germany.png" alt="" class="country-flag">
                                <span class="country-name">Germany</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/iran.png" alt="" class="country-flag">
                                <span class="country-name">Iran</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/italy.png" alt="" class="country-flag">
                                <span class="country-name">Italy</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/mexico.png" alt="" class="country-flag">
                                <span class="country-name">México</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/philipine.png" alt="" class="country-flag">
                                <span class="country-name">Philippines</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/portugal.png" alt="" class="country-flag">
                                <span class="country-name">Portugal</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/s-africa.png" alt="" class="country-flag">
                                <span class="country-name">South Africa</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/spanish.png" alt="" class="country-flag">
                                <span class="country-name">Spain</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/switzerland.png" alt="" class="country-flag">
                                <span class="country-name">Switzerland</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/uk.png" alt="" class="country-flag">
                                <span class="country-name">United Kingdom</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="country-item">
                                <img src="/Public/images/flags/english.png" alt="" class="country-flag">
                                <span class="country-name">United State</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div><!-- .modal-content -->
    </div><!-- .modla-dialog -->
</div><!-- .modal -->
<!-- JavaScript -->
<script src="/Public/assets/js/bundle.js?ver=3.0.0"></script>
<script src="/Public/assets/js/scripts.js?ver=3.0.0"></script>
<script src="/Public/assets/js/charts/gd-default.js?ver=3.0.0"></script>
<script src="/Public/assets/js/charts/chart-ecommerce.js?ver=3.0.0"></script>
<script src="/Public/assets/js/charts/chart-analytics.js?ver=3.0.0"></script>
<script>
    let container = $(".nk-tb-list.nk-tb-ulist");
    let innerContainer = container.find(".nk-tb-item:not(.nk-tb-head)");

    let limit = $("#limit-list").find("li");
    let order = $("#order-list").find("li").not(".order-title");
    let page = $("#select-page");


    let token = localStorage.getItem("token") ?
        localStorage.getItem("token") :
        "";

    window.levi = {
        defaults: {
            items: [],
            totalItems: 0,
            page: 1,
            loading: true,
            limit: 10,
            order: "DESC",
            headers: {
                Authorization: `Bearer ${token}`,
            },
        },
        money: (money) => {
            return (+money).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        },
        sign: '$',
        currency: 'USD',
        dateF: (dte) => {
            let options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            let theDay = new Date(dte);
            return theDay.toLocaleDateString('en-US', options)
        },
    }


    $(limit).click(function() {
        $(limit).removeClass("active");
        $(this).addClass("active");
        levi.defaults.items = []
        levi.defaults.limit = +$(this).text();
        init();
    });

    $(order).click(function() {
        $(order).removeClass("active");
        $(this).addClass("active");
        levi.defaults.order = $(this).text();
        init();
    });


    function setPageList(list = 1) {
        list = list < 1 ? 1 : list;
        $(page).html("");

        if (list > 1) {
            for (i = 0; i <= list - 1; i++) {
                $(page).append(`<option value=${i}">${i}</option>`);
            }
        }
        $("#total-desc-count").text(
            `${levi.defaults.totalItems} ${levi.defaults.totalItems > 1 ? levi.desc : levi.desc+'s'}`
        );
    }
</script>
<script src="/Public/js/axios.min.js"></script>
</body>

</html>