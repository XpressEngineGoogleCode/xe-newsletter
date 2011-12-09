<?php

/**
 * @class  NewsletterController
 * @author Arnia (xe_dev@arnia.ro)
 * @brief The controller class of the newsletter module
 * 
 **/

class newsletterAdminController extends newsletter 
{
    
    /**
     * @brief insert new Newsletter
     **/
    function procNewsletterAdminInsert()
    {
        $oNewsletterModel = &getModel('newsletter');
        $this->newsletter_srl = getNextSequence();
        $this->procNewsletterAdminUploadFile();
        
        $oNewsletterModel->saveNewsletter($this->newsletter_srl);

        $redirectUrl = getNotEncodedUrl('','module','admin','act','dispNewsletterAdminIndex');
	$this->setRedirectUrl($redirectUrl);
    }
    
    /**
     * @brief insert new Newsletter and send it
     **/
    function procNewsletterAdminInsertAndSend()
    {
        $oNewsletterModel = &getModel('newsletter');
        $this->newsletter_srl = getNextSequence();
        $this->procNewsletterAdminUploadFile();
        $oNewsletterModel->saveNewsletter($this->newsletter_srl,true);
        
        $oMail = new Mail();
        $attachment = Context::get('filename');
        if($attachment!=null || trim($attachment)!="" )
            $oMail->addAttachment ($attachment, './files/attach/newsletter/'.$attachment);
        $oMail->setTitle( Context::get('newsletter_title') );
        $oMail->setContent( Context::get('newsletter_content') );
        $logged_info = Context::get('logged_info');
        $oMail->setSender($logged_info->nick_name,$logged_info->email_address );
        $list_to = Context::get('list_config_to');
        $list_cc = Context::get('list_config_cc');
        $list_bcc = Context::get('list_config_bcc');
        $list_emails_to = $oNewsletterModel->getEmailList($list_to);
        $list_emails_cc = $oNewsletterModel->getEmailList($list_cc);
        $list_emails_bcc = $oNewsletterModel->getEmailList($list_bcc);
        $oMail->header = "CC: ".$list_emails_cc.$oMail->eol;
        $oMail->setBCC($list_emails_bcc);
        $oMail->setReceiptor( false, $list_emails_to );
        $oMail->send();
    
        $redirectUrl = getNotEncodedUrl('','module','admin','act','dispNewsletterAdminIndex');
	$this->setRedirectUrl($redirectUrl);
    }
    
    /**
     * @brief Update selected Newsletter
     **/
    function procNewsletterAdminUpdate()
    {
        $news_srl = Context::get('news_srl');
        $this->newsletter_srl = $news_srl;
        $oNewsletterModel = &getModel('newsletter');
        $this->procNewsletterAdminUploadFile();
        $oNewsletterModel->updateNewsletter($this->newsletter_srl);

        $redirectUrl = getNotEncodedUrl('','module','admin','act','dispNewsletterAdminIndex');
	$this->setRedirectUrl($redirectUrl);

    }
    
    /**
     * @brief Update selected Newsletter and send it
     **/
    function procNewsletterAdminUpdateAndSend()
    {
        $oNewsletterModel = &getModel('newsletter');
        $news_srl = Context::get('news_srl');
        $this->newsletter_srl = $news_srl;
        $this->procNewsletterAdminUploadFile();
        $attachment = Context::get('filename');
        $oNewsletterModel->updateNewsletter($news_srl,true);
        
        $oFileModel = &getModel("file");
        $file_link = $oFileModel->getFile($attachment);
        $file_link->download_url;
        
        $oMail = new Mail();
        if($attachment!=null )
            $oMail->addAttachment ($file_link->source_filename, $file_link->download_url);
        $oMail->setTitle( Context::get('newsletter_title') );
        $oMail->setContent( Context::get('newsletter_content') );
        $logged_info = Context::get('logged_info');
        $oMail->setSender($logged_info->nick_name,$logged_info->email_address );
        $list_to = Context::get('list_config_to');
        $list_cc = Context::get('list_config_cc');
        $list_bcc = Context::get('list_config_bcc');
        $list_emails_to = $oNewsletterModel->getEmailList($list_to);
        $list_emails_cc = $oNewsletterModel->getEmailList($list_cc);
        $list_emails_bcc = $oNewsletterModel->getEmailList($list_bcc);
        $oMail->header = "CC: ".$list_emails_cc.$oMail->eol;
        $oMail->setBCC($list_emails_bcc);
        $oMail->setReceiptor( false, $list_emails_to );
        $oMail->send();
  
        $redirectUrl = getNotEncodedUrl('','module','admin','act','dispNewsletterAdminIndex');
	$this->setRedirectUrl($redirectUrl);
    }
    
    /**
     * @brief Send selected Newsletter
     **/
    function procNewsletterAdminSend()
    {
        $news_srl = Context::get('news_srl');
        $this->newsletter_srl = $news_srl;
        $oNewsletterModel = &getModel('newsletter');
        $details = $oNewsletterModel->getNewsletterDetailsForSend($news_srl);
        
        $oFileModel = &getModel("file");
        $file_link = $oFileModel->getFile($details->file_attach);
        $file_link->download_url;
        
        $oMail = new Mail();
        if($details->file_attach!=null || trim($details->file_attach)!="" )
            $oMail->addAttachment ($file_link->source_filename, $file_link->download_url);
        $oMail->setTitle( $details->title );
        $oMail->setContent( $details->content);
        $logged_info = Context::get('logged_info');
        $oMail->setSender($logged_info->nick_name,$logged_info->email_address );
        $list_emails_to = $details->members_list_to;
        $list_emails_cc = $details->members_list_cc;
        $list_emails_bcc = $details->members_list_bcc;
        $oMail->header = "CC: ".$list_emails_cc.$oMail->eol;
        $oMail->setBCC($list_emails_bcc);
        $oMail->setReceiptor( false, $list_emails_to );
        $oMail->send();
        
        $oNewsletterModel->updateSendDateAfterEmailSend($news_srl);
        
        $redirectUrl = getNotEncodedUrl('','module','admin','act','dispNewsletterAdminHistory');
	$this->setRedirectUrl($redirectUrl);
    }
    
    /**
     * @brief Send selected Newsletter and make a copy
     **/
    function procNewsletterAdminSendAndCopy()
    {
        $news_srl = Context::get('news_srl');
        $this->newsletter_srl = $news_srl;
        $oNewsletterModel = &getModel('newsletter');
        $output = $oNewsletterModel->getNewsletterCopy($news_srl);
        $details = $oNewsletterModel->getNewsletterDetailsForSend($output);
        
        $oFileModel = &getModel("file");
        $file_link = $oFileModel->getFile($details->file_attach);
        $file_link->download_url;
        
        $oMail = new Mail();
        if($details->file_attach!=null )
            $oMail->addAttachment ($file_link->source_filename, $file_link->download_url);
        $oMail->setTitle( $details->title );
        $oMail->setContent( $details->content);
        $logged_info = Context::get('logged_info');
        $oMail->setSender($logged_info->nick_name,$logged_info->email_address );
        $list_emails_to = $details->members_list_to;
        $list_emails_cc = $details->members_list_cc;
        $list_emails_bcc = $details->members_list_bcc;
        $oMail->header = "CC: ".$list_emails_cc.$oMail->eol;
        $oMail->setBCC($list_emails_bcc);
        $oMail->setReceiptor( false, $list_emails_to );
        $result = $oMail->send();
        
        $oNewsletterModel->updateSendDateAfterEmailSend($news_srl);
        
        $redirectUrl = getNotEncodedUrl('','module','admin','act','dispNewsletterAdminHistory');
	$this->setRedirectUrl($redirectUrl);
    }
    
    /**
     * @brief Register the newsletter attached file
     **/
    function procNewsletterAdminUploadFile() {
        $site_srl = 0;
        $target = Context::get('newsletter_attach');
        $target_file = $target;
        // Error occurs when the target is neither a uploaded file nor a valid file
        if(!$target_file || !is_uploaded_file($target_file['tmp_name']) || !preg_match('/\.(gif|jpeg|jpg|png|doc|docx|pdf|xls|xslx|txt|csv)/i',$target_file['name'])) 
        {
            Context::set('error_message', Context::getLang('msg_invalid_request'));
        } 
        else 
        {
            $oFileController = &getController("file");
            $insert_op = $oFileController->insertFile($target_file,Context::get('module_srl'), $this->newsletter_srl) ;
            Context::set('filename',$insert_op->variables["file_srl"]);
        }
    }
}
?>
