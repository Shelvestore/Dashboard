
  

    <!-- @foreach ($data['category'] as $cat)
    
    {{$cat['detail'][0]['category_name']}}
    @endforeach -->
<template id="category-slider-template">
    <div class="">
        <div class="cat-banner" style="padding-top:30px;">
          <a class="category-slider-url" href="">
   <figure class="categories-image categories-icon">
      
               <img class="img-fluid category-slider-image"style="background:white;border:solid 4px;border-color :#000" src="" alt="Banner Image">

              <div class="col-md-12" style="background-image: url({{ asset('/gallary/top-img.png')}}); margin-top:-11px; padding:8px;"></div>
            <div class="categories-title">

              <h3 style="background:white;font-weight: bold; color:#000 !important; text-align:center " class="category-slider-title"></h3>
             </div>
          </figure>

          </a>
        </div>
      </div>
</template>
   <div class="general-product">
    <div class="container p-0">
      <div class="category-slider-show">
      </div>
    </div>
  </div>

