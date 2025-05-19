<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailQuote extends Mailable
{

    use Queueable,
        SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $data = [];

    private function initVal($key, $default = '')
    {
        return !empty($this->data[$key]) ? $this->data[$key] : $default;
    }

    public function __construct($data)
    {
        // echo '<pre>',print_r($data);die;
        $this->data = $data;
        $dataArr = [];
        define('SUBJECT', $this->initVal('subject'));
        if (isset($data['data']['senderEmail']) && $data['data']['senderEmail'] != '') {
            $emailToSend = $data['data']['senderEmail'];
        } else {
            $emailToSend = CONTACT_EMAIL;
        }
        if (isset($data['data']['fullName']) && $data['data']['fullName'] != '') {
            define('EMAIL_FROM_NAME', $this->initVal('fromName', $data['data']['fullName']));
        } else {
            define('EMAIL_FROM_NAME', $this->initVal('fromName', 'CustomBoxline Customer'));
        }
        if ($data['data']['senderEmail'] != '') {
            define('EMAIL_FROM', $this->initVal('emailFrom', $emailToSend));
            define('EMAIL_REPLY_TO', $this->initVal('replyTo', $data['data']['senderEmail']));
        } else {
            define('EMAIL_REPLY_TO', $this->initVal('replyTo', $emailToSend));
            define('EMAIL_FROM', $this->initVal('emailFrom', $emailToSend));
        }
        $dataArr = $this->initVal('data', []);
        $viewFile = $this->initVal('view');
        if (!stristr($viewFile, 'admin.')) {
            $viewFile = 'emails.layout.main';
            $dataArr['view_name'] = $this->initVal('view');
        }
        define('EMAIL_TO', $this->initVal('email', ''));
        $dataArr['email'] = EMAIL_TO;
        define('VIEW_FILE', $viewFile);
        define('VIEW_DATA', $dataArr);
        // sleep(60);
        // echo '<pre>',print_r($dataArr);die;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->withSwiftMessage(function ($message) {
            $headers = $message->getHeaders();
            $headers->addTextHeader('header', 'From: ' . EMAIL_FROM . ' Reply-To: ' . EMAIL_REPLY_TO . ' X-Mailer: PHP/' . phpversion());
        });
        $mailObject = $this->from(EMAIL_FROM, EMAIL_FROM_NAME)
            ->replyTo(EMAIL_REPLY_TO)
            ->subject(SUBJECT)
            ->view(VIEW_FILE)
            ->with('data', VIEW_DATA);
        // echo '<pre>', print_r($mailObject);die;
        if (!empty($this->data['cc'])) {
            $mailObject->cc($this->data['cc']);
        }
        if (!empty($this->data['bcc'])) {
            $mailObject->bcc($this->data['bcc']);
        }
        if (!empty($this->data['attachment'])) {
            if (is_array($this->data['attachment']) && count($this->data['attachment']) > 0) {
                $i = 1;
                foreach ($this->data['attachment'] as $attach) {
                    $mailObject->attach($attach['url']);
                }
            } else {
                $mailObject->attach($this->data['attachment']);
            }
        }
        // $oSendEmailsHistory = new \App\Models\SendEmailsHistory();
        // $oSendEmailsHistory->insert([
        //     'EMAIL_FROM' => EMAIL_FROM,
        //     'EMAIL_FROM_NAME' => EMAIL_FROM_NAME,
        //     'EMAIL_REPLY_TO' => EMAIL_REPLY_TO,
        //     'SUBJECT' => SUBJECT,
        //     'VIEW_FILE' => VIEW_FILE,
        //     'EMAIL_TO' => $this->data['email'],
        // ]);
        // echo '<pre>',print_r($mailObject);die;
        return $mailObject;
    }
}
