<?php

use Library\Route;

return  array(
    // site routes
    'default' => new Route('/', 'Default', 'index'),
    'index' => new Route('/index.php', 'Default', 'index'),
    'main' => new Route('/main', 'Main', 'index'),

    'main-ajax' => new Route('/main-ajax', 'Main', 'indexAjax'),
    'articles_by_tags-ajax' => new Route('/tags-ajax', 'Main', 'showByTagAjax'),

    'analytics' => new Route('/analytics', 'Main', 'indexAnalytics'),
    'politics_articles' => new Route('/articles/', 'Main', 'show'),
    'tags_articles' => new Route('/tags/articles/', 'Main', 'show'),
    'analytic_articles' => new Route('/analytic/', 'Main', 'showAnalytic'),
    'articles_by_tags' => new Route('/tags/', 'Main', 'showByTag'),

    'feedbacker' => new Route('/feedbacker/', 'Main', 'ShowFeedbacks'),
    'article_filter' => new Route('/article_filter/', 'Main', 'ArticleFilter'),

    'login' => new Route('/login', 'Security', 'login'),
    'logout' => new Route('/logout', 'Security', 'logout'),
    
    'admin_default' => new Route('/admin', 'Admin\\Default', 'index'),
    'admin_categories' => new Route('/admin/categories', 'Admin\\Main', 'index'),
    'admin_manage_articles' => new Route('/admin/manage_articles', 'Admin\\Main', 'manage_articles'),
    'admin_add_articles' => new Route('/admin/add_article', 'Admin\\Main', 'add_article'),
    'admin_edit_articles' => new Route('/admin/edit_article', 'Admin\\Main', 'edit_article'),
    'admin_edit_feedback' => new Route('/admin/edit_feedback', 'Admin\\Main', 'edit_feedback'),
    'admin_approve_feedback' => new Route('/admin/approve_feedback', 'Admin\\Main', 'approve_feedback'),
    'admin_manage_ads' => new Route('/admin/manage_ads', 'Admin\\Main', 'manage_ads'),

);