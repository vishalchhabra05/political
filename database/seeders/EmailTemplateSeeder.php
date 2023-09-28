<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use DB;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmailTemplate::truncate();

        DB::table('email_templates')->insert([
            [
                "slug" => "send-political-party-register-mail",
                "email_template" => "Political Party Registered",
                "subject" => "Political Party Registered",
                "message_greeting" => "Hello,",
                "message_body" => "Your Political party '{{political_party}}' has been registered. Here are your admin details.<br><b>Email :</b> {{email}}<br><b>Password :</b> {{password}}<br><br>Please click on this link to login <a href='{{link}}'>Click Here</a>",
                "message_signature" => "Regards Political Party",
                "dynamic_fields" => "{{political_party}},{{email}},{{password}},{{link}}",
                "last_updated_by" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "slug" => "send-sub-admin-register-mail",
                "email_template" => "Subadmin Registered",
                "subject" => "Subadmin Registered",
                "message_greeting" => "Hello,",
                "message_body" => "Your account has been successfully created under '{{political_party}}' party as subadmin user. Here are your admin details.<br><b>Email :</b> {{email}}<br><b>Password :</b> {{password}}<br><br>Please click on this link to login <a href='{{link}}'>Click Here</a>",
                "message_signature" => "Regards Political Party",
                "dynamic_fields" => "{{political_party}},{{email}},{{password}},{{link}}",
                "last_updated_by" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "slug" => "send-admin-forgot-password-mail",
                "email_template" => "Forgot Password",
                "subject" => "Forgot Password",
                "message_greeting" => "Hello,",
                "message_body" => "Here are your admin forgot password link for {{email}}. <br>Please click on this link to reset password <a href='{{link}}'>Click Here</a>",
                "message_signature" => "Regards Political Party",
                "dynamic_fields" => "{{email}},{{link}}",
                "last_updated_by" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "slug" => "send-political-party-updated-mail",
                "email_template" => "Political Party Updated",
                "subject" => "Political Party Updated",
                "message_greeting" => "Hello,",
                "message_body" => "Your Political party '{{political_party}}' has been updated. Here are your admin details.<br><b>Email :</b> {{email}}<br><b>Password :</b> {{password}}<br><br>Please click on this link to login <a href='{{link}}'>Click Here</a>",
                "message_signature" => "Regards Political Party",
                "dynamic_fields" => "{{political_party}},{{email}},{{password}},{{link}}",
                "last_updated_by" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "slug" => "send-contact-us-enquiry-reply-mail",
                "email_template" => "Contact Enquiry Reply",
                "subject" => "Enquiry Reply",
                "message_greeting" => "Hello,",
                "message_body" => "Please check the reply sent by admin to your enquiry.<hr><p><b>Your Message:</b> {{enquiryDetail_message}}</p><p><b>Reply:</b> {{enquiryDetail_reply}}</p><hr>",
                "message_signature" => "Regards Political Party",
                "dynamic_fields" => "{{enquiryDetail_message}},{{enquiryDetail_reply}}",
                "last_updated_by" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "slug" => "send-sub-member-confirmation-mail",
                "email_template" => "Political Party Sub Member Registered",
                "subject" => "Political Party Sub Member Registered",
                "message_greeting" => "Hello,",
                "message_body" => "Thank you for joining the '{{political_party}}' political party. Here are your login details.<br><b>Login ID :</b> {{national_id}}<br><b>Password :</b> {{password}}<br><br>To go on Party platy platform , Kindly  <a href='{{link}}'>Click Here</a>",
                "message_signature" => "Regards Political Party",
                "dynamic_fields" => "{{political_party}},{{national_id}},{{password}},{{link}},{{receiver_name}}",
                "last_updated_by" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "slug" => "send-sub-member-confirmation-link-mail",
                "email_template" => "Political Party Sub Member Confirmation",
                "subject" => "Political Party Sub Member Confirmation",
                "message_greeting" => "Hello,",
                "message_body" => "{{sender_name}} has invite you to join '{{political_party}}' on Political Party platform<br>Kindly click on the link to accept the invitation <a href='{{confirmation_link}}'>Click Here</a>",
                "message_signature" => "Regards Political Party",
                "dynamic_fields" => "{{political_party}},{{sender_name}},{{confirmation_link}}",
                "last_updated_by" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "slug" => "send-member-forgot-password-mail",
                "email_template" => "Forgot Password",
                "subject" => "Forgot Password",
                "message_greeting" => "Hello,",
                "message_body" => "Here are your otp {{otp}} for forgot password",
                "message_signature" => "Regards Political Party",
                "dynamic_fields" => "{{otp}}",
                "last_updated_by" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
        ]);
    }
}
