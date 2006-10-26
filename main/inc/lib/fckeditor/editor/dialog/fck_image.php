<?php
include_once('../../../../../inc/global.inc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title>Image Properties</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="robots" content="noindex, nofollow">
		<script src="common/fck_dialog_common.js" type="text/javascript"></script>
		<script src="fck_image/fck_image.js" type="text/javascript"></script>
		<link href="common/fck_dialog_common.css" rel="stylesheet" type="text/css" />
	</head>
	<body> <!--scroll="no" style="OVERFLOW: hidden"-->
		<div id="divInfo">
		  <div id="divExtra1"  style="DISPLAY: none">
			<table cellspacing="1" cellpadding="1" border="0" width="100%">
				<tr>
					<td>
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td width="100%"><span fckLang="DlgImgURL">URL</span>
								</td>
								<td id="tdBrowse" style="DISPLAY: none" nowrap rowspan="2"><br><input id="btnBrowse" onclick="BrowseServer();" type="button" value="Browse Server" fckLang="DlgBtnBrowseServer">
								</td>
							</tr>
							<tr>
								<td valign="top">
									<input id="txtUrl" style="WIDTH: 100%" type="text" onblur="UpdatePreview();">
								</td>
							</tr>

							<tr><td colspan="2">&nbsp;</td></tr>
						</table>
					</td>
				</tr>
			</table>
		  </div>
		  <?php 
		  
		  $sType = "Image";
		  include(api_get_path(INCLUDE_PATH).'course_document.inc.php');
		  
		  ?>
		</div>
		<div id="divExtra"  style="DISPLAY: none"> <!--added by shiv -->
			<table cellSpacing="1" cellPadding="1" width="100%" border="0">
				<tr>
					<td>
						<span fckLang="DlgImgAlt">Short Description</span><br />
						<input id="txtAlt" style="WIDTH: 100%" type="text"><br />
					</td>
				</tr>
				<tr height="100%">
					<td valign="top">
						<table cellspacing="0" cellpadding="0" width="100%" border="0" height="100%">
							<tr>
								<td valign="top">
									<br />
									<table cellspacing="0" cellpadding="0" border="0">
										<tr>
											<td nowrap><span fckLang="DlgImgWidth">Width</span>&nbsp;</td>
											<td>
												<input type="text" size="3" id="txtWidth" onkeyup="OnSizeChanged('Width',this.value);"></td>
											<td nowrap rowspan="2">
												<div id="btnLockSizes" class="BtnLocked" onmouseover="this.className = (bLockRatio ? 'BtnLocked' : 'BtnUnlocked' ) + ' BtnOver';"
													onmouseout="this.className = (bLockRatio ? 'BtnLocked' : 'BtnUnlocked' );" title="Lock Sizes"
													onclick="SwitchLock(this);"></div>
												<div id="btnResetSize" class="BtnReset" onmouseover="this.className='BtnReset BtnOver';"
													onmouseout="this.className='BtnReset';" title="Reset Size" onclick="ResetSizes();"></div>
											</td>
										</tr>
										<tr>
											<td nowrap><span fckLang="DlgImgHeight">Height</span>&nbsp;</td>
											<td>
												<input type="text" size="3" id="txtHeight" onkeyup="OnSizeChanged('Height',this.value);"></td>
										</tr>
									</table>
									<br />
									<table cellspacing="0" cellpadding="0" border="0">
										<tr>
											<td nowrap><span fckLang="DlgImgBorder">Border</span>&nbsp;</td>
											<td>
												<input type="text" size="2" value="" id="txtBorder" onkeyup="UpdatePreview();"></td>
										</tr>
										<tr>
											<td nowrap><span fckLang="DlgImgHSpace">HSpace</span>&nbsp;</td>
											<td>
												<input type="text" size="2" id="txtHSpace" onkeyup="UpdatePreview();"></td>
										</tr>
										<tr>
											<td nowrap><span fckLang="DlgImgVSpace">VSpace</span>&nbsp;</td>
											<td>
												<input type="text" size="2" id="txtVSpace" onkeyup="UpdatePreview();"></td>
										</tr>
										<tr>
											<td nowrap><span fckLang="DlgImgAlign">Align</span>&nbsp;</td>
											<td><select id="cmbAlign" onchange="UpdatePreview();">
													<option value="" selected></option>
													<option fckLang="DlgImgAlignLeft" value="left">Left</option>
													<option fckLang="DlgImgAlignAbsBottom" value="absBottom">Abs Bottom</option>
													<option fckLang="DlgImgAlignAbsMiddle" value="absMiddle">Abs Middle</option>
													<option fckLang="DlgImgAlignBaseline" value="baseline">Baseline</option>
													<option fckLang="DlgImgAlignBottom" value="bottom">Bottom</option>
													<option fckLang="DlgImgAlignMiddle" value="middle">Middle</option>
													<option fckLang="DlgImgAlignRight" value="right">Right</option>
													<option fckLang="DlgImgAlignTextTop" value="textTop">Text Top</option>
													<option fckLang="DlgImgAlignTop" value="top">Top</option>
												</select>
											</td>
										</tr>
									</table>
								</td>
								<td>&nbsp;&nbsp;&nbsp;</td>
								<td width="100%" valign="top">
									<table cellpadding="0" cellspacing="0" width="100%" style="TABLE-LAYOUT: fixed">
										<tr>
											<td><span fckLang="DlgImgPreview">Preview</span></td>
										</tr>
										<tr>
											<td valign="top">
												<iframe class="ImagePreviewArea" src="fck_image/fck_image_preview.html" frameborder="no" marginheight="0" marginwidth="0"></iframe>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<div id="divUpload" style="DISPLAY: none">
		<?php
		require("../plugins/loader.class.php");
		$loader = new Loader('frmUpload');
		$loader->init();
		?>
			<form id="frmUpload" name="frmUpload" method="post" target="UploadWindow" enctype="multipart/form-data" action="" onsubmit="return CheckUpload();">
				<!--<span fckLang="DlgLnkUpload">Upload</span>--><br />
				<table cellspacing="1" cellpadding="1" border="0" width="90%" align="center">
				<tr><td><input id="txtUploadFile" style="WIDTH: 100%" type="file" size="40" name="NewFile" /></td></tr>
				<tr><td><input id="btnUpload" type="submit" value="Send it to the Server" fckLang="DlgLnkBtnUpload" /></td></tr>
				</table>
				<iframe name="UploadWindow" style="DISPLAY: none" src="../fckblank.html"></iframe>
			</form>
		<?php
		$loader->close();
		?>
		</div>
		<div id="divLink" style="DISPLAY: none">
			<table cellspacing="1" cellpadding="1" border="0" width="100%">
				<tr>
					<td>
						<div>
							<span fckLang="DlgLnkURL">URL</span><br />
							<input id="txtLnkUrl" style="WIDTH: 100%" type="text" onblur="UpdatePreview();" />
						</div>
						<div id="divLnkBrowseServer" align="right">
							<input type="button" value="Browse Server" fckLang="DlgBtnBrowseServer" onclick="LnkBrowseServer();" />
						</div>
						<div>
							<span fckLang="DlgLnkTarget">Target</span><br />
							<select id="cmbLnkTarget">
								<option value="" fckLang="DlgGenNotSet" selected="selected">&lt;not set&gt;</option>
								<option value="_blank" fckLang="DlgLnkTargetBlank">New Window (_blank)</option>
								<option value="_top" fckLang="DlgLnkTargetTop">Topmost Window (_top)</option>
								<option value="_self" fckLang="DlgLnkTargetSelf">Same Window (_self)</option>
								<option value="_parent" fckLang="DlgLnkTargetParent">Parent Window (_parent)</option>
							</select>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div id="divAdvanced" style="DISPLAY: none">
			<table cellspacing="0" cellpadding="0" width="100%" align="center" border="0">
				<tr>
					<td valign="top" width="50%">
						<span fckLang="DlgGenId">Id</span><br />
						<input id="txtAttId" style="WIDTH: 100%" type="text">
					</td>
					<td width="1">&nbsp;&nbsp;</td>
					<td valign="top">
						<table cellspacing="0" cellpadding="0" width="100%" align="center" border="0">
							<tr>
								<td width="60%">
									<span fckLang="DlgGenLangDir">Language Direction</span><br />
									<select id="cmbAttLangDir" style="WIDTH: 100%">
										<option value="" fckLang="DlgGenNotSet" selected>&lt;not set&gt;</option>
										<option value="ltr" fckLang="DlgGenLangDirLtr">Left to Right (LTR)</option>
										<option value="rtl" fckLang="DlgGenLangDirRtl">Right to Left (RTL)</option>
									</select>
								</td>
								<td width="1%">&nbsp;&nbsp;</td>
								<td nowrap>
									<span fckLang="DlgGenLangCode">Language Code</span><br />
									<input id="txtAttLangCode" style="WIDTH: 100%" type="text">&nbsp;
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3">
						<span fckLang="DlgGenLongDescr">Long Description URL</span><br />
						<input id="txtLongDesc" style="WIDTH: 100%" type="text">
					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">
						<span fckLang="DlgGenClass">Stylesheet Classes</span><br />
						<input id="txtAttClasses" style="WIDTH: 100%" type="text">
					</td>
					<td></td>
					<td valign="top">&nbsp;<span fckLang="DlgGenTitle">Advisory Title</span><br />
						<input id="txtAttTitle" style="WIDTH: 100%" type="text">
					</td>
				</tr>
			</table>
			<span fckLang="DlgGenStyle">Style</span><br />
			<input id="txtAttStyle" style="WIDTH: 100%" type="text">
		</div>
	</body>
</html>
