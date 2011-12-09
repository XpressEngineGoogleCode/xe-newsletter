<?php
    /**
     * @class  NewsletterModel
     * @author Arnia (xe_dev@arnia.ro)
     * @brief The model class of the newsletter module
     * 
     **/

    class newsletterModel extends newsletter {
        
        
        /**
         * @brief Get a memebers list for each site
         **/
        function getSiteMemberList() {
            //$site_srl = Context::get('site_srl');
            $args->site_srl = 0;
            $query_id = 'newsletter.getSiteMemberList';
            $output = executeQueryArray($query_id, $args);
            $members = $output->data;
            $extra_vars = array();
            for ($i = 0; $i < count($members); $i++)
                $extra_vars[$members[$i]->member_srl] = $members[$i]->nick_name;
            return $extra_vars;
        }
        
        /**
        * @brief Saving a new newsletter
        **/
        function saveNewsletter($newsletter_srl,$send=false)
        {
            $logged_info = Context::get('logged_info');
            $args->newsletter_srl = $newsletter_srl;
            $args->sender_srl = $logged_info->member_srl;
            $lists = Context::get('newsletter_lists');
            $lists_array = explode("|", $lists);
            $args->members_list_to = $lists_array[0];
            $args->members_list_cc = $lists_array[1];
            $args->members_list_bcc = $lists_array[2];
            $args->title = Context::get('newsletter_title');
            $args->content = Context::get('newsletter_content');
            $args->file_attach = "".Context::get('filename');
            $time = date("YmdHis");
            $args->save_date = $time;
            $args->update_date = $time;
            if (!$send)
                $args->send_date = null;
            else
                $args->send_date = $time;
            
            $output = executeQueryArray("newsletter.insertNewNewsletter", $args);
        }
        
        /**
        * @brief Update newsletter details
        **/
        function updateNewsletter($news_srl,$send=false)
        {
            $args->news_srl = $news_srl;
            $lists = Context::get('newsletter_lists');
            $lists_array = explode("|", $lists);
            $args->members_list_to = $lists_array[0];
            $args->members_list_cc = $lists_array[1];
            $args->members_list_bcc = $lists_array[2];
            $args->title = Context::get('newsletter_title');
            $args->content = Context::get('newsletter_content');
            $args->file_attach = Context::get('filename');
            $time = date("YmdHis");
            $args->update_date = $time;
            if (!$send)
                $args->send_date = null;
            else
                $args->send_date = $time;
            $output = executeQueryArray("newsletter.updateNewsletter", $args);
        }
        
        /**
        * @brief Extract all newsletters for viewing the history
        **/
        function getNewsletterHistory() {
            $query_id = 'newsletter.getNewsletterHistory';
            $output = executeQueryArray($query_id);
            $query_id_member_nick = 'newsletter.getMembersNicknames';
            $newsletter_history = $output->data;
            $oFileModel = &getModel("file");
            for($i=0;$i<count($newsletter_history);$i++)
            {
                $file_link = $oFileModel->getFile($newsletter_history[$i]->file_attach);
                $newsletter_history[$i]->download_link = $file_link->download_url;
                $args->members = $newsletter_history[$i]->members_list_to;
                if ($args->members != "")
                {
                    $nicksTo = executeQueryArray($query_id_member_nick,$args);
                    $nicks_list = $nicksTo->data;
                    $temp_arr = array();
                    for($j=0;$j<count($nicks_list);$j++)
                        $temp_arr[] = $nicks_list[$j]->nick_name;
                    $newsletter_history[$i]->members_list_to = join(',',$temp_arr);
                }
                $args->members = $newsletter_history[$i]->members_list_cc;
                if ($args->members != "")
                {
                    $nicksCc = executeQueryArray($query_id_member_nick,$args);
                    $nicks_list = $nicksCc->data;
                    $temp_arr = array();
                    for($j=0;$j<count($nicks_list);$j++)
                        $temp_arr[] = $nicks_list[$j]->nick_name;
                    $newsletter_history[$i]->members_list_cc = join(',',$temp_arr);
                }
                
                $args->members = $newsletter_history[$i]->members_list_bcc;
                if ($args->members != "")
                {
                    $nicksBcc = executeQueryArray($query_id_member_nick,$args);
                    $nicks_list = $nicksBcc->data;
                    $temp_arr = array();
                    for($j=0;$j<count($nicks_list);$j++)
                        $temp_arr[] = $nicks_list[$j]->nick_name;
                    $newsletter_history[$i]->members_list_bcc = join(',',$temp_arr);
                }
            }
            return $newsletter_history;
        }
        
        /**
        * @brief get newsletter's details
        **/
        function getNewsletterDetails($news_srl)
        {
            $query_id = 'newsletter.getNewsletterDetails';
            $query_id_member_nick = 'newsletter.getMembersNicknames';
            $args->news_srl = $news_srl;
            $output = executeQueryArray($query_id,$args);
            $newsletter_details = $output->data[0];
            Context::set('news_srl',$news_srl);
            Context::set('title',$newsletter_details->title);
            Context::set('content',$newsletter_details->content);
            Context::set('attach',$newsletter_details->file_attach);
            unset($args->news_srl);
            $args1->members = $newsletter_details->members_list_to;
            if ($args1->members != "")
            {
                $nicksTo = executeQueryArray($query_id_member_nick,$args1);
                $nicks_list = $nicksTo->data;
                $temp_arr = array();
                for($j=0;$j<count($nicks_list);$j++)
                    $temp_arr[$nicks_list[$j]->member_srl] = $nicks_list[$j]->nick_name;
                Context::set('list_config_to',$temp_arr);
            }

            $args1->members = $newsletter_details->members_list_cc;
            if ($args1->members != "")
            {
                $nicksCc = executeQueryArray($query_id_member_nick,$args1);
                $nicks_list = $nicksCc->data;
                $temp_arr = array();
                for($j=0;$j<count($nicks_list);$j++)
                    $temp_arr[$nicks_list[$j]->member_srl] = $nicks_list[$j]->nick_name;
                Context::set('list_config_cc',$temp_arr);
            }

            $args1->members = $newsletter_details->members_list_bcc;
            if ($args1->members != "")
            {
                $nicksBcc = executeQueryArray($query_id_member_nick,$args1);
                $nicks_list = $nicksBcc->data;
                $temp_arr = array();
                for($j=0;$j<count($nicks_list);$j++)
                    $temp_arr[$nicks_list[$j]->member_srl] = $nicks_list[$j]->nick_name;
                Context::set('list_config_bcc',$temp_arr);
            }
        }
        
        /**
        * @brief get newsletter's details for send
        **/
        function getNewsletterDetailsForSend($news_srl)
        {
            $query_id = 'newsletter.getNewsletterDetails';
            $query_id_member_nick = 'newsletter.getMembersNicknames';
            $args->news_srl = $news_srl;
            $output = executeQueryArray($query_id,$args);
            $newsletter_details = $output->data[0];
            
            $args1->members = $newsletter_details->members_list_to;
            if ($args1->members != "")
            {
                $newsletter_details->members_list_to = $this->getEmailList($args1->members);
            }

            $args1->members = $newsletter_details->members_list_cc;
            if ($args1->members != "")
            {
                $newsletter_details->members_list_cc = $this->getEmailList($args1->members);
            }

            $args1->members = $newsletter_details->members_list_bcc;
            if ($args1->members != "")
            {
                $newsletter_details->members_list_bcc = $this->getEmailList($args1->members);
            }
            return $newsletter_details;
        }
        
        /**
        * @brief Make newsletter copy
        **/
        function getNewsletterCopy($news_srl,$send=true)
        {
            $query_id = 'newsletter.getNewsletterDetails';
            $args->news_srl = $news_srl;
            $output = executeQueryArray($query_id,$args);
            $newsletter_details = $output->data[0];
            $time = date("YmdHis");
            $newsletter_details->save_date = $time;
            $newsletter_details->update_date = $time;
            if ($send)
                $newsletter_details->send_date = $time;
            else 
                $newsletter_details->send_date = null;
            unset($newsletter_details->news_srl);
            $newsletter_details->newsletter_srl = getNextSequence();
            $output = executeQueryArray("newsletter.insertNewNewsletter", $newsletter_details);
            return $newsletter_details->newsletter_srl;
        }
        
        /**
        * @brief get List of members emails for sending emails to them
        **/
        function getEmailList($list)
        {
            $query_member_details = 'newsletter.getMembersNicknames';
            $args->members = $list;
            $emails_list = "";
            if ($args->members != "")
            {
                $output = executeQueryArray($query_member_details,$args);
                $temp_arr = array();
                for($i = 0;$i < count($output->data); $i++)
                    $temp_arr[$i] = $output->data[$i]->email_address;
                $emails_list .= join(',',$temp_arr);
            }
            return $emails_list;
        }
        
        /**
        * @brief Update the send date after the newsletter was sent
        **/
        function updateSendDateAfterEmailSend($news_srl)
        {
            $query_update = 'newsletter.updateSendDate';
            $time = date("YmdHis");
            $args->send_date = $time;
            $args->news_srl = $news_srl;
            
            $output = executeQueryArray($query_update, $args);
        }
    }
?>
