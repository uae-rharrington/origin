var config = {
    map: {
        '*': {
            'Ajaxsearch': 'Swissup_Ajaxsearch/js/ajaxsearch',
            'AjaxsearchLoader': 'Swissup_Ajaxsearch/js/ajaxsearch-loader',
            'typeaheadbundle': 'Swissup_Ajaxsearch/js/typeaheadbundle'
        }
    },
    config: {
        mixins: {
            'Magento_Search/form-mini': {
                'Swissup_Ajaxsearch/js/form-mini-mixin': true
            }
        }
    }
};
