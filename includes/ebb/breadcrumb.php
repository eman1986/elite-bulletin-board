<?php
/**
Filename: breadbrumb.php
Last Modified: 10/22/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
 */

namespace ebb;

class breadcrumb {
    /**
     * Breadcrumbs stack
    */
    private $breadcrumbs = array();

    /**
     * Options
    */
    private $divider = '<span class="divider">/</span>';
    private $tag_open = '<ul class="breadcrumb">';
    private $tag_close 	= '</ul>';

    /**
     * Append crumb to stack
     * @param string $title
     * @param string $href
     * @return void
    */
    public function append_crumb($title, $href) {
        // no title or href provided
        if (!$title || !$href) return;

        // add to end
        $this->breadcrumbs[] = array('title' => $title, 'href' => $href);
    }

    /**
     * Prepend crumb to stack
     * @param string $title
     * @param string $href
     * @return void
    */
    public function prepend_crumb($title, $href) {
        // no title or href provided
        if (!$title || !$href) return;

        // add to start
        array_unshift($this->breadcrumbs, array('title' => $title, 'href' => $href));
    }

    /**
     * Generate breadcrumb
     * @access public
     * @return string
    */
    public function output() {
        // breadcrumb found
        if ($this->breadcrumbs) {

            //counter variable.
            $i = 0;

            // set output variable
            $output = $this->tag_open;

            // add html to output
            foreach ($this->breadcrumbs as $key => $crumb) {
                // if last element
                if (end(array_keys($this->breadcrumbs)) == $key) {
                    $output .= '<li class="active">'.$crumb['title'].'</li>';

                    // else add link and divider
                } else {
                    $output .= '<li><a href="'.$crumb['href'].">". $crumb['title']."</a>".$this->divider.'</li>';
                }

                //increment counter.
                $i++;

            }

            // return html
            return $output . $this->tag_close . PHP_EOL;
        }

        // return blank string
        return '';
    }
}