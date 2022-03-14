<?php
    $items  = [];

    if(isset($list) && $list){
        foreach($list AS $i=>$row){
            $id     = $row["id"];
            $detail_link = Controllers::$init->AdminCRLink("manage-website-2",["blogs","edit"])."?id=".$id;
            $page_link          = Controllers::$init->CRLink("articles_detail",[$row["route"]]);
            $page_link_short    = Utility::short_text($page_link,0,50,true);

            $title              = Utility::short_text($row["title"],0,40,true);

            $show_category = ___("needs/none");
            if($row["category"]){
                $show_category = $row["category"].' <a href="'.Controllers::$init->CRLink("articles_category",[$row["category_route"]]).'" target="_blank" class="targeturl sbtn"><i class="fa fa-external-link" aria-hidden="true"></i></a>';
            }

            $item   = [];
            array_push($item,$i);
            array_push($item,'<span title="'.htmlspecialchars($row["title"],ENT_QUOTES).'">'.$title.'</span>');
            array_push($item,'<a target="_blank" href="'.$page_link.'">'.$page_link_short.'</a>');
            array_push($item,$show_category);
            array_push($item,DateManager::format(Config::get("options/date-format")." - H:i",$row["ctime"]));
            $perms  = '';

            $perms .= '<a href="'.$detail_link.'" data-tooltip="'.___("needs/button-edit").'" class="sbtn"><i class="fa fa-pencil"></i></a> ';

            if($privDelete)
                $perms .= '<a id="delete-'.$id.'" href="javascript:deletePage('.$id.');void 0;" data-tooltip="'.___("needs/button-delete").'" class="red sbtn"><i class="fa fa-trash"></i></a> ';

            array_push($item,$perms);



            $items[] = $item;
        }
    }


    return $items;