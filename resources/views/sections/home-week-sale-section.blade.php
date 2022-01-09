<section class="">
    <div class="container">
        <div class="">
            <div class="row justify-content-center">
               
                <div class="col-md-12" >  <img src='{{ asset('/gallary/collection_img.png')}}' width="25%" style="margin-bottom:-7%">  </span> <h3 style="text-align:center; letter-spacing: 14px;font-size 35px;">B E S T	S E L L  E R</h3><img src='{{ asset('/gallary/collection_img.png')}}' width="25%" style="margin-top: -6.4%;margin-left: 74%;"> </span></div>

                
            </div>
            <div class="row weekly-sale">

                <!-- <div class="col-12 col-lg-6">

                    <div class="product product-ad" id="weekly-sale-first-div">
                        
                    </div>
                </div> -->
                @include(isset(getSetting()['card_style']) ?
              'includes.cart.product_card_'.getSetting()['card_style'] : "includes.cart.product_card_style1")

                

            </div>
        </div>
    </div>
</section>
