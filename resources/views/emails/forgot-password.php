<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
<title><?php echo $site_name; ?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style type="text/css">
    /* CLIENT-SPECIFIC STYLES */
    #outlook a{padding:0;} /* Force Outlook to provide a "view in browser" message */
    .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
    .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing */
    body, table, td, a{-webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;} /* Prevent WebKit and Windows mobile changing default text sizes */
    table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;} /* Remove spacing between tables in Outlook 2007 and up */
    img{-ms-interpolation-mode:bicubic;} /* Allow smoother rendering of resized image in Internet Explorer */

    /* iOS BLUE LINKS */
    .appleBody a {color:#68440a; text-decoration: none;}
    .appleFooter a {color:#999999; text-decoration: none;}
    a{
        outline:none;
        color: inherit;
        text-decoration:underline;
    }
    div,
    p,
    a,
    li,
    td {
        -webkit-text-size-adjust: none;
    }
    .btn:hover{
        opacity: 0.8 !important;
    }
    a:hover{text-decoration:none !important;}
    a[x-apple-data-detectors]{color:inherit !important; text-decoration:none !important;}
    a img{border:none;}
    table td{mso-line-height-rule:exactly;}
    @media only screen and (max-width: 600px) {
        br{
            display:none !important;
        }
        td[class="p-0"]{padding:0 !important;}
        table[class="flexible"]{width:100% !important;}
    }
</style>
</head>
<body style="margin:0; padding:0; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;" bgcolor="#ffffff">
	<table align="center" class="flexible" cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td bgcolor="#ffffff" align="center" valign="top" style="margin:0;padding:0;border:0;width:100%!important;background: #ffffff">
				<table class="flexible" align="center" cellpadding="0" cellspacing="0" border="0" width="600">
					<tr>
						<td>
							<table class="flexible" align="center" cellpadding="0" cellspacing="0" border="0" width="600">
								<tr>
									<td style="background: #ffffff; padding: 24px 15px 40px;">
										<table class="flexible" align="left" cellpadding="0" cellspacing="0" border="0" width="600">
											<tr>
												<td>
													<table class="flexible" width="600" align="left" style="margin:0 auto;" cellpadding="0" cellspacing="0">
														<tr>
															<td style="padding:16px 0 32px;">
																<table cellpadding="0" cellspacing="0" width="100%">
																	<tr>
																		<td>
																			<table width="100%" cellpadding="0" cellspacing="0">
																				<tr>
																					<td align="left">
																						<a target="_blank" href="#">
																							<img src="https://raw.githubusercontent.com/ushahidi/platform-pattern-library/master/assets/img/wordmark_GoldonLight_2x.png" border="0" width="117" height="30" alt="Ushahidi" style="vertical-align: top; width: 117px; height: 24px;" />
																						</a>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td>
													<table class="flexible" width="600" align="center" style="margin:0 auto;" cellpadding="0" cellspacing="0">
														<tr>
															<td style="padding:24px; border: 1px solid #D3D4D5; border-radius: 8px;">
																<table cellpadding="0" cellspacing="0" width="100%">
																	<tr>
																		<td style="padding: 0 0 40px;">
																			<table cellpadding="0" cellspacing="0" width="100%">
																				<tr>
																					<td align="left" style="font:24px/29px Open Sans, Arial, Helvetica, sans-serif; font-weight: 500; color: #181B21; padding: 0 0 8px;">
																						Reset your password
																					</td>
																				</tr>
																				<tr>
																					<td align="left" style="font:16px/24px Open Sans, Arial, Helvetica, sans-serif; font-weight: normal; color: #383E45; padding: 0 0 24px;">
																						Hi <?php echo $user_name ?>.
																					</td>
																				</tr>
																				<tr>
																					<td align="left" style="font:16px/24px Open Sans, Arial, Helvetica, sans-serif; font-weight: normal; color: #383E45; padding: 0 0 8px;">
																						We received a request to reset the password for your account.
																						<?php if ($client_url) : ?> Click the button below to reset your password. <?php else: ?>
																						Paste the code below into the token field on the password reset page. <?php endif; ?>
																					</td>
																				</tr>

																				<?php if ($client_url) : ?>
																				<tr>
																					<td align="center" style="padding: 0 0 24px;">
																						<a href="<?php echo $client_url.'/forgotpassword/confirm/'.urlencode($reset_string); ?>" class="btn" style="font:16px/20px Open Sans, Arial, Helvetica, sans-serif; display: block; text-decoration: none; font-weight: 500; color: #181B21; padding: 10px; background: #FFC235; border-radius: 4px;">
																							Recover Password
																						</a>
																					</td>
																				</tr>
																				<?php endif; ?>

																				<tr>
																					<td align="left" style="font:16px/24px Open Sans, Arial, Helvetica, sans-serif; font-weight: normal; color: #383E45;">
																						<?php if ($client_url) : ?>
																						Click the link <a href="<?php echo $client_url.'/forgotpassword/confirm/'.urlencode($reset_string); ?>" target="_blank" style="color: #AA8223; font-weight: 600; letter-spacing: 0.03125em;">
																						<?php echo $client_url.'/forgotpassword/confirm/'.urlencode($reset_string); ?></a>
																						or paste the code below into the token field on the recovery page.
																						<?php endif; ?>
																					    Note that the reset token will expire after <?php echo $duration ?> minutes
																					</td>
																				</tr>
																				<tr>
																					<td align="left" style="padding: 8px 0;">
																						<table cellpadding="0" cellspacing="0" width="100%">
																							<tr>
																								<td align="center" style="font:14px/21px Open Sans, Arial, Helvetica, sans-serif; font-weight: 700; text-align: center; color: #383E45; padding: 16px; word-break:break-all; background: #F5F5F5; border-radius: 8px;">
																									<?php echo $reset_code; ?>
																								</td>
																							</tr>
																						</table>
																					</td>
																				</tr>

																				<tr>
																					<td align="left" style="font:16px/24px Open Sans, Arial, Helvetica, sans-serif; font-weight: normal; color: #383E45; padding: 24px 0;">
																						If you didn't initiate this request, please ignore and contact us on <a href="mailto:support@ushahidi.com" target="_blank" style="color: #AA8223; font-weight: 600; letter-spacing: 0.03125em;">support@ushahidi.com</a>
																					</td>
																				</tr>
																				<tr>
																					<td align="left" style="font:16px/24px Open Sans, Arial, Helvetica, sans-serif; font-weight: normal; color: #383E45;">
																						Best regards, <br> The Ushahidi Team
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td style="border-top: 1px solid #D3D4D5; padding: 8px 0 0;">
																			<table cellpadding="0" cellspacing="0" width="100%">
																				<tr>
																					<td align="left" style="padding: 24px 0 16px;">
																						<a target="_blank" href="#">
																							<img src="https://raw.githubusercontent.com/ushahidi/platform-pattern-library/master/assets/img/wordmark_GoldonLight_2x.png" border="0" width="117" height="25" alt="Ushahidi" style="vertical-align: top; width: 117px; height: 24px;" />
																						</a>
																					</td>
																				</tr>
																				<tr>
																					<td align="left" style="font:14px/18px Open Sans, Arial, Helvetica, sans-serif; font-weight: normal; color: #383E45; padding: 0 0 16px;">
																						Ushahidi empowers people through citizen-generated data to develop solutions that strengthen their communities.
																					</td>
																				</tr>
																				<!-- <tr>
																					<td align="left" style="font:14px/18px Open Sans, Arial, Helvetica, sans-serif; font-weight: normal; color: #6C7074; padding: 8px 0 0;">
																						No longer interested? <a href="#" style="font-weight: 600; color: #6C7074;">Unsubscribe</a> or <a href="#" style="font-weight: 600; color: #6C7074;">manage your subscriptions</a>
																					</td>
																				</tr> -->
																			</table>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
