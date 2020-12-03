<?php global $map_street_view; ?>
<ul class="nav nav-pills" id="pills-tab" role="tablist">
	<li class="nav-item">

		<?php if( !houzez_map_in_section() && houzez_get_listing_data('property_map') ) { ?>
		<a class="nav-link active" id="pills-gallery-tab" data-toggle="pill" href="#pills-gallery" role="tab" aria-controls="pills-gallery" aria-selected="true">
		<?php } else { ?>
			<a class="nav-link active" id="pills-gallery-tab" data-toggle="modal" href="#property-lightbox" aria-controls="property-lightbox" aria-selected="true">

			
		<?php } ?>
			<i class="houzez-icon icon-picture-sun"></i>
		</a>
	</li>

	

	<?php if( !houzez_map_in_section() && houzez_get_listing_data('property_map')) { ?>
		<li class="nav-item">
			<a class="nav-link" id="pills-map-tab" data-toggle="pill" href="#pills-map" role="tab" aria-controls="pills-map" aria-selected="true">
				<i class="houzez-icon icon-maps"></i>
			</a>
		</li>

		<?php if( houzez_get_map_system() == 'google' && $map_street_view != 'hide' ) { ?>
		<li class="nav-item">
			<a class="nav-link" id="pills-street-view-tab" data-toggle="pill" href="#pills-street-view" role="tab" aria-controls="pills-street-view" aria-selected="false">
				<i class="houzez-icon icon-location-user"></i>
			</a>
		</li>
		<?php } ?>
	<?php } ?>
</ul><!-- nav -->	