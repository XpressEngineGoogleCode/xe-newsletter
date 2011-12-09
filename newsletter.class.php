<?php
    /**
     * @class  newsletter
     * @author Arnia (xe_dev@arnia.ro)
     * @brief newsletter module's high class
     **/

    class newsletter extends ModuleObject {
        
        var $newsletter_srl;
        /**
         * @brief implemented if additional tasks are required when installing
         **/
        function moduleInstall() {
            // register the action forward (for using on the admin mode)

            return new Object();
        }
        
        /**
         * @brief Check updates
         **/
        function checkUpdate()
        {
            return false;
        }
        /**
         * @brief Regenerate cache file
         **/
        function recompileCache() {
        }
    }
?>
