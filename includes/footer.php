<?php
$useful_links_sql = "SELECT * FROM Web_3dprints.useful_links;";
?>
<!-- Footer -->
<footer class="site-footer style-1">
	<!-- Footer Top -->
	<div class="footer-top">
		<div class="container">
			<div class="row">
				<div class="col-xl-3 col-md-4 col-sm-6">
					<div class="widget widget_about me-2">
						<div class="footer-logo logo-white">
							<a href="index.php"><img src="images/logo.png" alt="/"></a>
						</div>
						<ul class="widget-address">
							<li>
								<p><span>Address</span> : 8180 Elm Ln, Rogers, AR 72756</p>
							</li>
							<li>
								<p><span>E-mail</span> : sales@kumpeapps.com</p>
							</li>
							<!-- <li>
									<p><span>Phone</span> : 501.831.2980</p>
								</li> -->
						</ul>
					</div>
				</div>
				<div class="col-xl-2 col-md-3 col-sm-4 col-6">
					<div class="widget widget_services">
						<h5 class="footer-title">Useful Links</h5>
						<ul>
							<?php
							if ($links_query = mysqli_query($conn, $useful_links_sql)) {
								while ($links_data = mysqli_fetch_array($links_query)) {
									$link_title = $links_data['text'];
									$link_link = $links_data['url'];
									echo "<li><a href='$link_link'>$link_title</a></li>";
								}
							}
							?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Footer Top End -->

	<!-- Footer Bottom -->
	<div class="footer-bottom">
		<div class="container">
			<div class="row fb-inner">
				<div class="col-lg-6 col-md-12 text-start">
					<p class="copyright-text">©
						<?php echo date("Y"); ?> <a href="javascript:void(0);">KumpeApps LLC</a> All Rights Reserved.
					</p>
				</div>
			</div>
		</div>
	</div>
	<!-- Footer Bottom End -->
	<a href="#" onclick="window.displayPreferenceModal();return false;" id="termly-consent-preferences">Consent
		Preferences</a>
</footer>
<!-- Footer End -->