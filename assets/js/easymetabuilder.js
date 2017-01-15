window.easyMetaBuilder = {};
	( function( window, $, that ) {

		// Constructor.
		that.init = function() {
			that.cache();
			that.bindEvents();
		}

		// Cache all the things.
		that.cache = function() {
			that.$c = {
				window: $( window ),
				body: $( 'body' ),
				typeSelect: $('.typeselect select'),
			};
		}

		// Combine all events.
		that.bindEvents = function() {

			// Run show/hide of field options on load.
			that.typeSelectSet();

			// show/hide meta boxes based on select otpion
			that.$c.body.on( 'change', '.typeselect select', function() {

                var typeSelect = $(this).val();
				$(this).parent().parent().nextAll( '.field-option' ) .hide();

				if ( $(this).parent().parent().nextAll().hasClass(typeSelect) ) {
					that.metaShowHide( $(this).parent().parent().nextAll( 'div.' + typeSelect ) );
				}

			});

			$('.emb-field-data').on( 'click', function() {
				$( this ).children().toggle();
			});


			$.each( $('.cmb2-wrap'), function() {

				if ( $(this).hasClass('options') ) {
					$(this).parent().addClass('no-padding');
				}

			});

        }

		// Shows field options based on type select.
		that.typeSelectSet = function() {

			$.each( $('.typeselect select'), function() {

				var typeSelect = $(this).val();
				$('.field-option').hide();

				if ( $(this).parent().parent().nextAll().hasClass(typeSelect) ) {
					that.metaShowHide( $(this).parent().parent().nextAll( 'div.' + typeSelect ) );
				}

			});

		}

        // Function to handle which items should be showing/hiding
        that.metaShowHide = function(showem) {
            showem.slideDown('fast');
        }

		// Engage!
		$( that.init );

	})( window, jQuery, window.easyMetaBuilder );
