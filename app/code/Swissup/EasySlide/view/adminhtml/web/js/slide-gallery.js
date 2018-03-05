define([
    'jquery',
    'mage/template',
    'jquery/ui',
    'baseImage',
    'productGallery'
], function($, mageTemplate) {
    //'use strict';

    function bytesToSize(bytes) {
        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'],
            i;

        if (bytes === 0) {
            return '0 Byte';
        }

        i = window.parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));

        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }
    /**
     * Slide gallery widget
     */
  $.widget('swissup.slideGallery', $.mage.productGallery, {
      /**
       * Set image as main
       * @param {Object} imageData
       * @private
       */
      setBase: function (imageData) {
          return false;
      },


      onDialogOpen: function (event) {
            var imageData = this.$dialog.data('imageData'),
                imageSizeKb = bytesToSize(imageData.sizeLabel),
                image = document.createElement('img'),
                sizeSpan = this.$dialog.find(this.options.imageSizeLabel)
                    .find('[data-message]'),
                resolutionSpan = this.$dialog.find(this.options.imageResolutionLabel)
                    .find('[data-message]'),
                sizeText = sizeSpan.attr('data-message').replace('{size}', imageSizeKb),
                resolutionText;

            image.src = imageData.url;

            resolutionText = resolutionSpan
                .attr('data-message')
                .replace('{width}^{height}', image.width + 'x' + image.height);

            sizeSpan.text(sizeText);
            resolutionSpan.text(resolutionText);

            $('#slide-enabled').on('change', function(e) {
                var target = $(e.target);
                var targetName = target.attr('name');
                var isActive = target.val();
                $('input[type="hidden"][name="'+ targetName + '"]').val(isActive);
                imageData.is_active = isActive;
            }.bind(this));

            $('#slide-title').on('change', function(e) {
                var target = $(e.target);
                var targetName = target.attr('name');
                var title = target.val();
                $('input[type="hidden"][name="'+ targetName + '"]').val(title);
                imageData.title = title;
            }.bind(this));

            $('#slide-link').on('change', function(e) {
                var target = $(e.target);
                var targetName = target.attr('name');
                var link = target.val();
                $('input[type="hidden"][name="'+ targetName + '"]').val(link);
                imageData.link = link;
            }.bind(this));

            $('#slide-link-target').on('change', function(e) {
                var target = $(e.target);
                var targetName = target.attr('name');
                var linkTarget = target.val();
                $('input[type="hidden"][name="'+ targetName + '"]').val(linkTarget);
                imageData.target = linkTarget;
            }.bind(this));

            $('#slide-description').on('change', function(e) {
                var target = $(e.target);
                var targetName = target.attr('name');
                var desc = target.val();
                $('input[type="hidden"][name="'+ targetName + '"]').val(desc);
                imageData.description = desc;
            }.bind(this));

            $('#slide-desc-position').on('change', function(e) {
                var target = $(e.target);
                var targetName = target.attr('name');
                var desc = target.val();
                $('input[type="hidden"][name="'+ targetName + '"]').val(desc);
                imageData.desc_position = desc;
            }.bind(this));

            $('#slide-desc-background').on('change', function(e) {
                var target = $(e.target);
                var targetName = target.attr('name');
                var desc = target.val();
                $('input[type="hidden"][name="'+ targetName + '"]').val(desc);
                imageData.desc_position = desc;
            }.bind(this));


            $(event.target)
                .find('[data-role=type-selector]')
                .each($.proxy(function (index, checkbox) {
                    var $checkbox = $(checkbox),
                        parent = $checkbox.closest('.item'),
                        selectedClass = 'selected',
                        isChecked = this.options.types[$checkbox.val()].value == imageData.file;

                    $checkbox.prop(
                        'checked',
                        isChecked
                    );
                    parent.toggleClass(selectedClass, isChecked);
                }, this));
        },


      _initDialog: function () {
            var $dialog = $(this.dialogContainerTmpl());

            $dialog.modal({
                'type': 'slide',
                title: $.mage.__('Slide Detail'),
                buttons: [],
                opened: function () {
                    $dialog.trigger('open');
                },
                closed: function () {
                    $dialog.trigger('close');
                }
            });

            $dialog.on('open', this.onDialogOpen.bind(this));
            $dialog.on('close', function () {
                var $imageContainer = $dialog.data('imageContainer');
                  $("#slide-enabled").trigger('change');
                  $("#slide-title").trigger('change');
                  $("#slide-link").trigger('change');
                  $("#slide-link-target").trigger('change');
                  $("#slide-description").trigger('change');
                  $("#slide-desc-position").trigger('change');
                  $("#slide-desc-background").trigger('change');
                $imageContainer.removeClass('active');
                $dialog.find('#hide-from-product-page').remove();
            });

            $dialog.on('change', '[data-role=type-selector]', function () {
                var parent = $(this).closest('.item'),
                    selectedClass = 'selected';

                parent.toggleClass(selectedClass, $(this).prop('checked'));
            });

            $dialog.on('change', '[data-role=type-selector]', $.proxy(this._notifyType, this));

            $dialog.on('change', '[data-role=visibility-trigger]', $.proxy(function (e) {
                var imageData = $dialog.data('imageData');

                this.element.trigger('updateVisibility', {
                    disabled: $(e.currentTarget).is(':checked'),
                    imageData: imageData
                })
            }, this));

            $dialog.on('change', '[data-role="image-description"]', function (e) {
                var target = $(e.target),
                    targetName = target.attr('name'),
                    desc = target.val(),
                    imageData = $dialog.data('imageData');

                this.element.find('input[type="hidden"][name="' + targetName + '"]').val(desc);

                imageData.label = desc;
                imageData.label_default = desc;

                this.element.trigger('updateImageTitle', {
                    imageData: imageData
                });
            }.bind(this));


            var imageData = $dialog.data('imageData');


            this.$dialog = $dialog;
        }
  });

  return $.swissup.slideGallery;
});
