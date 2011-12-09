<?php
    /**
     * @class  NewsletterAdminView
     * @author Arnia (xe_dev@arnia.ro)
     * @brief The admin view class of the newsletter module
     **/

    class newsletterAdminView extends newsletter {
        /**
         * @brief Initialization
         **/
        function init() {
            //Set template path
            $this->setTemplatePath($this->module_path.'tpl');
        }
        
        /**
         * @brief Call newsletter form function
         **/
        function dispNewsletterAdminIndex()
        {
            $news_srl = Context::get('news_srl');
	    $this->dispNewsletterForm($news_srl);
        }
        
        /**
         * @brief Call newsletter history function
         **/
        function dispNewsletterAdminHistory()
        {
	    $this->dispNewsletterHistory();
        }
        
        /**
         * @brief Display newsletter form for creating new newsletter
         **/
        function dispNewsletterForm($news_srl)
        {
            $oNewsletterModel = &getModel('newsletter');
            $members_list = $oNewsletterModel->getSiteMemberList();
            Context::set('extra_vars',$members_list);
            if ($news_srl == null)
            {
                Context::set('list_config_to',array());
                Context::set('list_config_cc',array());
                Context::set('list_config_bcc',array());
            }
            else
            {
                $oNewsletterModel->getNewsletterDetails($news_srl);
            }
            $this->setTemplateFile('newsletter_admin_index');
        }
        
        /**
         * @brief Display newsletters history list
         **/
        function dispNewsletterHistory()
        {
            $oModuleModel = &getModel('newsletter');
            $newsletter_history = $oModuleModel->getNewsletterHistory();
            Context::set('extra_vars',$newsletter_history);
            $this->setTemplateFile('newsletter_history');
        }

   }
?>