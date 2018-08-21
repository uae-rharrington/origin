var klevu_uc = { 
   // landing page
  showLandingPageData : function ( product ){
    var toReturn = '',
        toAppendwithSalePrice = '',
        priceWithCurrency = '',
        appendCurrencyAtLast = false,
        salepriceClass = 'kuSalePrice',
        priceFormatter, priceToSet,
        showToLabel = false,
        additionalParams = '',
        landingSequence,
        landingItemParam = '',
        keywords = document.getElementById( 'searchedKeyword' ).value,
        trackingParams = "";

    product = klevu_productCustomizations( product );
    landingSequence = product.children ? product.children.split(" ") : [];

    if( landingSequence.indexOf(keywords) !== -1 ) { 
      landingItemParam = '?item=' + keywords;
    }

    if( product.productImage.trim().length === 0 ){
      product.productImage = klevu_userOptions.noImageUrl;
    }

    if( klevu_userOptions.openProductClicksInNewWindow ){
      additionalParams = ' target="_blank"';
    } else{
      additionalParams = ' onclick="klevu_analytics.stopClickDefault( event );"';
    }
    trackingParams = '{' +
      'data: {' +
        'code: \'' + escape(product.productCode) + '\',' +
        'url: \'' + escape(product.productUrl) + '\',' +
        'name: \'' + escape(product.productName) + '\',' +
        'salePrice: \'' + escape(product.salePrice) + '\',' +
        'rating: \''+ escape(product.rating) + '\',' +
        'position: ' + product.productPosition + ',' +
        'category: \'' + escape(product.category) + '\'' +
      '},' +
      'apiKey: null,' +
      'keywordsLP: \'' + escape(keywords) + '\'' +
    '}';
    // code for the result block
    toReturn += '<li>';

    toReturn += '<div class="klevuImgWrap"><a href="' + product.productUrl.replace(/"/g,"%22") +
                landingItemParam + '" target="_blank" ';
    if( klevu_commons.isMobileDevice() ){
      toReturn += ' onclick="return klevu_analytics.trackClickedProduct(event, ' + trackingParams + ');" >';
    } else{
      toReturn += ' onmousedown="return klevu_analytics.trackClickedProduct(event, ' + trackingParams + ');" ' + additionalParams + '>';
    }

    toReturn += '<img src="' +
                product.productImage + '" onerror="this.onerror=null;this.src=\'' +
                klevu_userOptions.noImageUrl + '\';" alt="' + klevu_commons.escapeHtml(product.productName) + '"/></a></div>';
    if( 'undefined' !== typeof klevu_showDiscountBadge &&
        klevu_showDiscountBadge && product.discount != '' &&
        product.discount != '0' && product.discount != '0.0' ){
      if( klevu_uiLabels.discountBadgeText.indexOf("#") === -1 ){
        toReturn += '<div class="kuDiscountBadge">' +
                    klevu_uiLabels.discountBadgeText + ' ' +
                    Number( product.discount ).toFixed(0) +
                    '%</div>';
      } else {
        toReturn += '<div class="kuDiscountBadge">' +
                    klevu_uiLabels.discountBadgeText.replace( "#", Number( product.discount ).toFixed(0) + "%" ) +
                    '</div>';
      }
    }
    toReturn += '<div class="kuNameDesc">';
    toReturn += '<div class="kuName"><a href="' + product.productUrl.replace(/"/g,"%22") + landingItemParam +
                '" target="_blank" ';
    if( klevu_commons.isMobileDevice() ){
      toReturn += ' onclick="return klevu_analytics.trackClickedProduct(event, ' + trackingParams + ');" >';
    } else{
      toReturn += ' onmousedown="return klevu_analytics.trackClickedProduct(event, ' + trackingParams + ');" ' + additionalParams + '>';
    }
    toReturn += product.productName + '</a></div>';
    toReturn += '<div class="kuDesc">' + product.productDescription + '</div>';
    if( product.rating.trim().length > 0 && !isNaN(Number(product.rating)) &&
        Number(product.rating) <= 5 && Number(product.rating) >= 0 ){
      var starWidth = 20 * Number(product.rating);
      toReturn += '<div class="kuStarsSmall">'+
                  '<div class="kuRating" style="width:' + starWidth +
                  '%;"></div></div>';
    }
    toReturn += '</div>';

    toReturn += '<div class="kuPrice">';
    if( klevu_showPrices ){
      if( klevu_userOptions.showOnlyOriginalAndSalePrices ){
        toReturn += klevu_commons.showOriginalAndSalePrices( 'LANDING', product,
                    salepriceClass, 'kuSalePrice kuSpecialPrice' );
      } else {
        toReturn += klevu_commons.showProductPrices( 'LANDING', product,
                    salepriceClass, 'kuSalePrice kuSpecialPrice' );
      }
    }

    if( klevu_userOptions.vatCaption.trim().length > 0 ){
      toReturn += '<div class="klevu-vat-caption">(' + klevu_userOptions.vatCaption + ')</div>';
    }

    if( product.totalProductVariants && product.totalProductVariants != '0' ){
      if( klevu_uiLabels.variants.indexOf("#") === -1 ){
        toReturn += '<div class="klevu-variants">+' + product.totalProductVariants +
                    ' ' + klevu_uiLabels.variants + '</div>';
      } else {
        toReturn += '<div class="klevu-variants">' +
                    klevu_uiLabels.variants.replace( "#", product.totalProductVariants) + '</div>';
      }
    }
    if( klevu_userOptions.outOfStockCaption.trim().length > 0 ){
      if( ( product.inStock ) && product.inStock === 'no' ){
        toReturn += '<div class="klevu-out-of-stock">' +
                    klevu_userOptions.outOfStockCaption + '</div>';
      }
    }

    toReturn += '</div>';
    if( klevu_commons.showAddToCartButton( product.inStock, product.hideAddToCart ) ){
      if( !(product.isCustomOptionsAvailable && product.isCustomOptionsAvailable === "yes") &&
          (!product.totalProductVariants || product.totalProductVariants == '0') ){
        toReturn += '<div class="kuAddtocart">' +
                    '<input type="text" name="klevu-qty" id="klevu-qty-' +
                    escape( product.productCode ) + '" placeholder="' +
                    klevu_uiLabels.addToCartPlaceholder + '"/>' +
                    '<a href="javascript:klevu_lpSendProductToCart(\'' +
                    escape( product.productCode ) + '\', \'' +
                    escape( product.productUrl ) + '\', \'klevu-qty-' +
                    escape( product.productCode ) + '\');" ' +
                    'class="kuAddtocartBtn">' + klevu_userOptions.addToCartButton + '</a>' +
                    '</div>';
      } else{
        toReturn += '<div class="kuAddtocart">' +
                    '<a href="' + product.productUrl.replace(/"/g,"%22") + landingItemParam +
                    '" class="kuAddtocartBtn">' + klevu_userOptions.addToCartButton + '</a>' +
                    '</div>';
      }
      toReturn += '<div class="klevu-clearboth-listview"></div>';
    }
    toReturn += '<div class="kuClearLeft"></div>';
    toReturn += '</li>';

    return toReturn;
  },

  showAutocompleteProducts: function( product ){
    var productHtml = '',
        trackingParams = '',
        imgAlt,
        imgAltDiv = document.createElement('div'),
        sequence,
        landingItemParam = '';

    product = klevu_productCustomizations( product );
    sequence = product.children ? product.children.split(" ") : [];

    if( sequence.indexOf(klevu_searchedTerm) !== -1 ) { 
      landingItemParam = '?item=' + klevu_searchedTerm;
    }

    if( product.productImage.trim().length === 0 ){
      product.productImage = klevu_userOptions.noImageUrl;
    }
    if( klevu_userOptions.openProductClicksInNewWindow ){
      additionalParams = ' target="_blank"';
    } else{
      additionalParams = ' onclick="klevu_analytics.stopClickDefault( event );"';
    }
    trackingParams = '{' +
      'data: {' +
        'code: \'' + escape(product.productCode) + '\',' +
        'url: \'' + escape(product.productUrl) + '\',' +
        'name: \'' + escape(product.productName) + '\',' +
        'salePrice: \'' + escape(product.salePrice) + '\',' +
        'rating: \''+ escape(product.rating) + '\',' +
        'position: ' + product.productPosition + ','+
        'category: \'' + escape(product.category) + '\'' +
      '}' +
    '}';
    imgAltDiv.innerHTML = product.productName;
    imgAlt = imgAltDiv.textContent || imgAltDiv.innerText || "";
    productHtml += '<li>';

 
    productHtml += '<a href="' + product.productUrl.replace(/"/g,"%22") + landingItemParam + '" class="klevu-result-box-l2" ' ;
    if( klevu_commons.isMobileDevice() ){
      productHtml += ' onclick="return  klevu_analytics.trackClickedProduct(event, ' + trackingParams + ');"  >';
    } else{
      productHtml += ' onmousedown="return  klevu_analytics.trackClickedProduct(event, ' + trackingParams + ');" ' +
                     additionalParams + ' >';
    }

    productHtml += '<div class="klevu-img-wrap-l2"><img src="' + product.productImage +
                   '" onerror="this.onerror=null;this.src=\'' + klevu_userOptions.noImageUrl +
                   '\';" alt="' + klevu_commons.escapeHtml(imgAlt) + '" /></div>';
    if( 'undefined' !== typeof klevu_showDiscountBadge &&
        klevu_showDiscountBadge && product.discount != '' &&
        product.discount != '0' && product.discount != '0.0' ){
      if( klevu_uiLabels.discountBadgeText.indexOf("#") === -1 ){
        productHtml += '<div class="klevu-discount-badge-l2">' +
                    klevu_uiLabels.discountBadgeText + ' ' + Number( product.discount ).toFixed(0) +
                    '%</div>';
      } else{
        productHtml += '<div class="klevu-discount-badge-l2">' +
                    klevu_uiLabels.discountBadgeText.replace( "#", Number( product.discount ).toFixed(0) + "%" ) +
                    '</div>';

      }
    }
    productHtml += '<div class="klevu-name-desc-l2">';
    productHtml += '<div class="klevu-name-l2">' + product.productName + '</div>' +
                   '<div class="klevu-desc-l2">' + product.productDescription + '</div>';

    // show prices
    if( klevu_showPrices ){
      productHtml += '<div class="klevu-price-l2">';
      if( klevu_userOptions.showOnlyOriginalAndSalePrices ){
        productHtml += klevu_commons.showOriginalAndSalePrices( 'SLIM', product, 'klevu-saleprice-l2',
                       'klevu-saleprice-l2 klevu-special-price-l2' );
      } else {
        productHtml += klevu_commons.showProductPrices( 'SLIM', product, 'klevu-saleprice-l2',
                       'klevu-saleprice-l2 klevu-special-price-l2' );
      }
      if( klevu_userOptions.vatCaption.trim().length > 0 ){
        productHtml += '<span class="klevu-vat-caption-l2">(' +
                       klevu_userOptions.vatCaption + ')</span>';
      }
      productHtml += '</div>';
    }
    if( product.rating.trim().length > 0 && !isNaN(Number(product.rating)) &&
        Number(product.rating) <= 5 && Number(product.rating) >= 0 ){
      var starWidth = 20 * Number(product.rating);
      productHtml += '<div class="klevu-stars-small-l2">'+
                     '<div class="klevu-rating-l2" style="width:' + starWidth +
                     '%;"></div></div>';
    }
    if( klevu_userOptions.outOfStockCaption.trim().length > 0 ){
      if( product.inStock && product.inStock === 'no' ){
        productHtml += '<div class="klevu-out-of-stock-l2">' +
                       klevu_userOptions.outOfStockCaption + '</div>';
      }
    }
    productHtml +=  '</div><div class="klevu-clear-left"></div></a></li>';
    return productHtml;
  }
}
