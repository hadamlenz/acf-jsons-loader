<?php
/**
 * Loader for acf .json files
 * circumvents the local-json functionality, do not use if you have that turned on
 * @author      H. Adam Lenz <hadamlenz@me.com>
 * @link        https://github.com/hadamlenz/Gamajo-Template-Loader
 * @copyright   2020 H. Adam Lenz
 * @license     GPL-2.0-or-later
 * @version     1.0.3
 * 
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Acf_Theme_Jsons' ) && class_exists('acf_pro') ) {

    class Acf_Jsons_loader{
        /**
         * the root of the theme directory 
         * absolute
         *
         * @var [string]
         */
        public $root = "";

        /**
         * an array of all of the field groups 
         *
         * @var 
         */
        public $fieldgroup_array = array();

        /**
         * path to the file that has all of the fields
         *
         * @var [string] 
         */
        public $all_jsons_file;

        /**
         * build the object
         *
         * @param [string $root the root directory of the project
         * @param [array] $json, could be an array, could be a single file
         */
        function __construct( $root , $json ){
            
            if( $root && "" !== $root ){ //we need a root to get the files
                $this->root = $root;
            } else {
                exit();
            }
            
            if( is_array( $json ) && !empty( $json ) ){  
                //if it's an array of files
                //array looks like groupname => file location relative to the root
                $this->build_fieldgroup_array_from_array_of_files( $json );

            } elseif( is_string( $json ) && "" !== $json ) { 
                //if it's a single file, should be a file location
                $this->build_fieldgroup_array_from_single_file( $json );
                
            } else { //it's not gonna work, bail
                exit();
            }

            add_action( 'acf/init', array( $this, 'load_fieldgroup_array_fields' ) );
        }

        /**
         * set the array to the fieldgroup_array
         *
         * @param [array] groupname => file location relative to the root
         * @return void
         */
        private function build_fieldgroup_array_from_array_of_files( $jsons ){

            //loop thru the dev provided array
            foreach( $jsons as $slug => $path ){
                //if slug is here already, we dont need to load this json
                if( false === array_search( $slug, $this->get_groups_keys_array() ) ){
                    //if not load the json string into the fieldgroup_array
                    if( false !== ( $this_json = $this->get_json_file( $path ) ) ){
                        $fieldgroup = json_decode ( $this_json, true );
                        $this->fieldgroup_array[] = $fieldgroup[0]; 
                    }
                } 
            }
        }

        /**
         * take the json file that has all of the fields 
         * create the fieldgroup array
         *
         * @param  $name
         * @return void
         */
        public function build_fieldgroup_array_from_single_file( $file_path ){
            //we start this one by getting the file
            if( false !== ( $this_json = $this->get_json_file( $file_path ) ) ){
                $fieldgroups = json_decode ( $this_json, true );
                foreach( $fieldgroups as $fieldgroup ){
                    //if slug is here already, we dont need to load this json
                    if( false === array_search( $fieldgroup['key'], $this->get_groups_keys_array() ) ){
                        $this->fieldgroup_array[] = $fieldgroup; 
                    } 
                }
            }
        }

        /**
         * load an array of local json fields
         *
         * @return void
         */
        public function load_fieldgroup_array_fields(){
            if( is_array( $this->fieldgroup_array) && !empty( $this->fieldgroup_array ) ){
                foreach( $this->fieldgroup_array as $fieldgroup){
                    acf_add_local_field_group( $fieldgroup );
                }
            }
        }

        /**
         * grabs file contents 
         *
         * @param [type] $path to the acf.json file
         * @return [string] $json or false if file doesn;t exist
         */
        private function get_json_file( $path ){
            if( file_exists( $this->root . $path ) ){
                return file_get_contents( $this->root . $path );
            } else {
                return false;
            }
        }

        /**
         * get the fieldgroup keys of the fields that are loaded
         *
         * @return void
         */
        private function get_groups_keys_array(){
            $current_groups = acf_get_field_groups();
            $groups_keys_array = array();

            //make an array of keys to search what's loaded
            if( is_array( $current_groups ) && !empty( $current_groups ) ){
                foreach( $current_groups as $a_current_group ){
                    $groups_keys_array[] = $a_current_group['key'];
                }
            }

            return $groups_keys_array;
        }
    }
}