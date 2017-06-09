<?php
/*
 * Copyright (C) 2017   Splash Sync       <contact@splashsync.com>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*/

namespace Splash\Local\Objects\Post;

use Splash\Client\Splash      as Splash;

/**
 * @abstract    Wordpress Taximony Data Access
 */
trait HooksTrait {

    static $PostClass    =   "\Splash\Local\Objects\Post";
    
    /**
    *   @abstract     Register Post & Pages, Product Hooks
    */
    static public function registeHooks()   {

        add_action( 'save_post',        [ static::$PostClass , "Updated"],  10, 3);                
        add_action( 'deleted_post',     [ static::$PostClass , "Deleted"],  10, 3);       
                
    }    

    static public function Updated( $Id , $Post, $Updated ) {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__ . "(" . $Id . ")");            
        //====================================================================//
        // Prepare Commit Parameters
        $Action         =   $Updated ? SPL_A_UPDATE : SPL_A_CREATE;        
        if ($Post->post_type == "post") {
            $ObjectType     =   "Post";
        } else if ($Post->post_type == "page") {
            $ObjectType     =   "Page";
        } else if ($Post->post_type == "product") {
            $ObjectType     =   "Product";
        } else if ($Post->post_type == "shop_order") {
            $ObjectType     =   "Order";
        } else {
            return Splash::Log()->Deb("Unknown Object Type => " . $Post->post_type);
        }    
        $Comment    =   $ObjectType .  ($Updated ? " Updated" : " Created") . " on Wordpress";
        //====================================================================//
        // Prevent Repeated Commit if Needed
        if ( ($Action == SPL_A_UPDATE) && Splash::Object($ObjectType)->isLocked() ) {
            return;
        }
        //====================================================================//
        // Do Commit
        Splash::Commit($ObjectType, $Id, $Action, "Wordpress", $Comment);
    }
    
    static public function Deleted( $post_id ) {
        
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__ . "(" . $post_id . ")");
        
        $post = get_post($post_id);
        if ($post->post_type == "post") {
            Splash::Commit("Post", $post_id, SPL_A_DELETE, "Wordpress", "Post Deleted");
        }     
        if ($post->post_type == "page") {
            Splash::Commit("Page", $post_id, SPL_A_DELETE, "Wordpress", "Page Deleted");
        }     
        if ($post->post_type == "product") {
            Splash::Commit("Product", $post_id, SPL_A_DELETE, "Wordpress", "Product Deleted");
        }     
        if ($post->post_type == "shop_order") {
            Splash::Commit("Order", $post_id, SPL_A_DELETE, "Wordpress", "Order Deleted");
        }     
        
    }    
    
}
