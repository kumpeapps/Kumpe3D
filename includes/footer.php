<?php
$useful_links_sql = "SELECT * FROM Web_3dprints.useful_links;";
$conn = mysqli_connect(
	$_ENV['mysql_host'],
	$_ENV['mysql_user'],
	$_ENV['mysql_pass'],
	'Web_3dprints'
) or die("Couldn't connect to server.");
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
							<a href="index.php"><img src="<?php echo $site_params['store_logo_url']; ?>" alt="/"></a>
						</div>
						<ul class="widget-address">
							<li>
								<p><span>Address</span> : <br><?php echo $site_params['store_address']; ?><br>
								<?php echo $site_params['store_city']; ?>, 
								<?php echo $site_params['store_state']; ?> 
								<?php echo $site_params['store_zip']; ?></p>
							</li>
							<li>
								<p><span>E-mail</span> : <?php echo $site_params['store_email']; ?></p>
							</li>
							<!-- <li>
									<p><span>Phone</span> : <?php echo $site_params['store_phone']; ?></p>
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
<!-- Messenger Chat Plugin Code -->
<div id="fb-root"></div>

<!-- Your Chat Plugin code -->
<div id="fb-customer-chat" class="fb-customerchat">
</div>

<script nonce="<?php echo $nonce; ?>">
  var chatbox = document.getElementById('fb-customer-chat');
  chatbox.setAttribute("page_id", "156967714165483");
  chatbox.setAttribute("attribution", "biz_inbox");
</script>

<!-- Your SDK code -->
<script nonce="<?php echo $nonce; ?>">
  window.fbAsyncInit = function() {
	FB.init({
	  xfbml            : true,
	  version          : 'v18.0'
	});
  };

  (function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
	fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>
<!-- Footer End -->