<table align="center" border="0" cellpadding="0" cellspacing="0" id="bodyTable" style="border-collapse: collapse; background-color: #f7f7f7; height: 100%; margin: 0; padding: 0; width: 100%;" width="100%">
	<tbody>
		<tr>
			<td align="center" id="bodyCell" style="border-top: 0; height: 100%; margin: 0; padding: 0; width: 100%;" valign="top">
			<table border="0" cellpadding="0" cellspacing="0" class="templateContainer" style="border-collapse: collapse; max-width: 900px; background: #f9f9f9; border: 0;" width="100%">
				<tbody>
					<tr>
						<td id="templatePreheader" style="background: #4096ee; border-top: 0; border-bottom: 1; padding: 10px 0;" valign="top">
						<table border="0" cellpadding="0" cellspacing="0" class="mcnTextBlock" style="border-collapse: collapse; min-width: 100%;" width="100%">
							<tbody class="mcnTextBlockOuter">
								<tr>
									<td class="mcnTextBlockInner" valign="top">
									<table align="left" border="0" cellpadding="0" cellspacing="0" class="mcnTextContentContainer" style="border-collapse: collapse; min-width: 100%;" width="100%">
										<tbody>
											<tr>
												<td class="mcnTextContent" style="word-break: break-word; color: #fff; font-size: 30px; line-height: 150%; text-align: center; padding: 9px 18px;" valign="top">@if(!empty($data['email_heading'])) {!! $data['email_heading'] !!} @else {!! $data['email_template'] !!} @endif</td>
											</tr>
										</tbody>
									</table>
									</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
						<td id="templateBody" style="background-color: #fff; border-bottom: 1px solid #d8d8d8; border-top: 1px solid #d8d8d8; padding: 30px 30px;" valign="top">
						<p>{!! $data['message_greeting'] !!}&nbsp;</p>

						<!-- <p>{!! $data['message_body'] !!}</p> -->
						{!! $data['message_body'] !!}

						<p>{!! $data['message_signature'] !!}</p>
						</td>
					</tr>
					<tr>
						<td id="templateFooter" style="background-color: #000000c7; padding: 25px 0;" valign="top">
						<table border="0" cellpadding="0" cellspacing="0" class="mcnTextBlock" style="min-width: 100%;" width="100%">
							<tbody class="mcnTextBlockOuter">
								<tr>
									<td class="mcnTextBlockInner" valign="top">
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td style="text-align: center; color: #fffcfc; font-size: 21px; font-weight: 500;" valign="top">Political Party</td>
											</tr>
										</tbody>
									</table>
									</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>