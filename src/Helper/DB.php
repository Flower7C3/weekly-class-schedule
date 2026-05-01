<?php

/** @noinspection SqlNoDataSourceInspection */

namespace WCS4\Helper;

use WCS4\Entity\Item;
use WCS4\Entity\Journal_Item;
use WCS4\Entity\Lesson_Item;
use WCS4\Entity\Progress_Item;
use WCS4\Entity\WorkPlan_Item;
use WCS4\Repository\Contract\SchemaCreatableInterface;
use WCS4\Repository\Journal;
use WCS4\Repository\Progress;
use WCS4\Repository\Schedule;
use WCS4\Repository\Snapshot;
use WCS4\Repository\WorkPlan;

/**
 * WCS4 Database operations
 */
class DB
{
    /**
     * Since all three custom post types are in the same table, we can assume the the ID will be unique so there's no need to check for post type.
     */
    public static function delete_item_when_delete_post($post_id): void
    {
        global $wpdb;
        $table_schedule = Schedule::get_schedule_table_name();
        $query = "DELETE FROM $table_schedule WHERE subject_id = %d OR teacher_id = %d OR student_id = %d OR classroom_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id, $post_id, $post_id, $post_id)));

        $table_schedule_teacher = Schedule::get_schedule_teacher_table_name();
        $query = "DELETE FROM $table_schedule_teacher WHERE teacher_id = %d ";
        $wpdb->query($wpdb->prepare($query, array($post_id)));

        $table_schedule_student = Schedule::get_schedule_student_table_name();
        $query = "DELETE FROM $table_schedule_student WHERE student_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id)));

        $table_journal = Journal::get_journal_table_name();
        $query = "DELETE FROM $table_journal WHERE subject_id = %d  OR teacher_id = %d  OR student_id = %d OR classroom_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id, $post_id, $post_id, $post_id)));

        $table_journal_teacher = Journal::get_journal_teacher_table_name();
        $query = "DELETE FROM $table_journal_teacher WHERE teacher_id = %d ";
        $wpdb->query($wpdb->prepare($query, array($post_id)));

        $table_journal_student = Journal::get_journal_student_table_name();
        $query = "DELETE FROM $table_journal_student WHERE student_id = %d";
        $wpdb->query($wpdb->prepare($query, array($post_id)));
    }

    /**
     * Install all the data for wcs4 (tworzy tabele dla wszystkich repozytoriów implementujących SchemaCreatableInterface).
     */
    public static function create_schema(): void
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $repositories = [
            Schedule::class,
            Journal::class,
            WorkPlan::class,
            Progress::class,
            Snapshot::class,
        ];
        foreach ($repositories as $repository) {
            /** @var SchemaCreatableInterface $repository */
            $repository::create_db_tables();
        }
        add_option('wcs4_db_version', WCS4_DB_VERSION);
    }

    /**
     * Load example data for wcs4
     */
    public static function load_example_data(): void
    {
        $teachers = [
            'Wilburn Marsland',
            'Alexia Strosnider',
            'Dorris Craner',
            'Iluminada Nader',
        ];
        $students = [
            'Aurea Orlandi',
            'Gertha Patout',
            'Jutta Nicely',
            'Shellie Gatts',
            'Seymour Mortellaro',
            'Mathew Ahumada',
            'Vanda Hindman',
            'Hyman Beresford',
            'Liza Tarango',
            'Tracee Marlatt',
            'Maryjane Tapley',
            'Salvador Madsen',
            'Rosa Buchholz',
            'Norene Waldrep',
            'Von Heier',
            'Etha Roiger',
            'Carletta Holiday',
            'Merideth Valladares',
            'Dia Schamber',
            'Arlette Herdt',
        ];
        $classrooms = [
            'Room 1',
            'Room 2',
            'Room 3',
            'Room 4',
            'Room 5',
        ];
        $subjects = [
            'Math',
            'Physics',
            'Chemistry',
            'Geography',
            'Biology',
            'English',
        ];
        foreach ($subjects as $subject) {
            wp_insert_post([
                'post_title' => $subject,
                'post_status' => 'private',
                'post_type' => WCS4_POST_TYPE_SUBJECT,
            ]);
        }
        foreach ($teachers as $teacher) {
            wp_insert_post([
                'post_title' => $teacher,
                'post_status' => 'private',
                'post_type' => WCS4_POST_TYPE_TEACHER,
            ]);
        }
        foreach ($students as $student) {
            wp_insert_post([
                'post_title' => $student,
                'post_status' => 'private',
                'post_type' => WCS4_POST_TYPE_STUDENT,
            ]);
        }
        foreach ($classrooms as $classroom) {
            wp_insert_post([
                'post_title' => $classroom,
                'post_status' => 'private',
                'post_type' => WCS4_POST_TYPE_CLASSROOM,
            ]);
        }
    }

    /**
     * Reset settings
     */
    public static function reset_settings(): void
    {
        delete_option('wcs4_settings');
        do_action('wcs4_default_settings');
    }

    /**
     * Deletes all WCS4 data: plugin options, CPT posts, WCS4 taxonomy terms, then drops plugin tables.
     */
    public static function delete_everything(): void
    {
        global $wpdb;

        delete_option('wcs4_db_version');
        delete_option('wcs4_settings');
        delete_option('wcs4_version');

        $post_types = array(
            WCS4_POST_TYPE_SUBJECT,
            WCS4_POST_TYPE_TEACHER,
            WCS4_POST_TYPE_STUDENT,
            WCS4_POST_TYPE_CLASSROOM,
        );

        foreach ($post_types as $type) {
            $posts = get_posts(array(
                'numberposts' => -1,
                'post_type' => $type,
                'post_status' => 'any'
            ));

            foreach ($posts as $post) {
                wp_delete_post($post->ID, true);
            }
        }

        foreach (array_keys(WCS4_TAXONOMY_TYPES_WHITELIST) as $taxonomy) {
            self::clear_taxonomy($taxonomy);
        }

        $wpdb->query('DROP TABLE IF EXISTS ' . Schedule::get_schedule_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Schedule::get_schedule_student_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Schedule::get_schedule_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Journal::get_journal_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Journal::get_journal_student_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Journal::get_journal_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . WorkPlan::get_work_plan_subject_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . WorkPlan::get_work_plan_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . WorkPlan::get_work_plan_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Progress::get_progress_subject_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Progress::get_progress_teacher_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Progress::get_progress_table_name());
        $wpdb->query('DROP TABLE IF EXISTS ' . Snapshot::get_snapshot_table_name());
    }

    /**
     * Import WCS4 data from another WordPress table prefix.
     *
     * Copies:
     * - custom post types: wcs4_subject, wcs4_teacher, wcs4_student, wcs4_classroom (+ postmeta)
     * - taxonomies: wcs4_branch, wcs4_specialization, wcs4_group, wcs4_location (+ relationships)
     * - wcs4_* plugin tables (schedule, journal, work_plan, progress, snapshot + link tables)
     *
     * To avoid ID collisions, posts and terms are inserted via WordPress APIs and then foreign keys
     * in wcs4_* tables are remapped using in-memory ID maps.
     *
     * @return array summary (counts + message)
     * @throws \InvalidArgumentException
     */
    public static function import_from_prefix(string $sourcePrefix, string $cutoffDate, bool $runCutoff = true): array
    {
        global $wpdb;

        $sourcePrefix = trim($sourcePrefix);
        if ($sourcePrefix === '') {
            throw new \InvalidArgumentException(__('Source prefix is required.', 'wcs4'));
        }
        if (!preg_match('/^[A-Za-z0-9_]+$/', $sourcePrefix) || !str_ends_with($sourcePrefix, '_')) {
            throw new \InvalidArgumentException(__('Source prefix must contain only letters/numbers/underscore and end with underscore (_).', 'wcs4'));
        }
        if ($sourcePrefix === $wpdb->prefix) {
            throw new \InvalidArgumentException(__('Source prefix must be different than current prefix.', 'wcs4'));
        }
        if ($cutoffDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $cutoffDate)) {
            throw new \InvalidArgumentException(__('Cutoff date must be in YYYY-MM-DD format.', 'wcs4'));
        }
        if ($cutoffDate === '') {
            $cutoffDate = date('Y-m-d');
        }

        // Ensure schema exists.
        self::create_schema();

        // 1) Clear current WCS4 data (tables + CPT + taxonomies) but keep plugin settings/options intact.
        self::clear_wcs4_data_only();

        // 2) Copy CPT posts and build old->new ID map.
        $postIdMap = self::copy_wcs4_posts_and_meta_from_prefix($sourcePrefix);

        // 3) Copy taxonomies + relationships (terms may be attached to posts).
        self::copy_wcs4_taxonomies_from_prefix($sourcePrefix, $postIdMap);

        // 4) Copy plugin tables, remapping subject/teacher/student/classroom IDs.
        $tableCounts = self::copy_wcs4_tables_from_prefix($sourcePrefix, $postIdMap);

        // 5) Cutoff (optional).
        $cutoffCounts = [];
        if ($runCutoff) {
            $cutoffCounts = self::run_cutoff($cutoffDate);
        }

        $summary = [
            'source_prefix' => $sourcePrefix,
            'target_prefix' => $wpdb->prefix,
            'cutoff' => $cutoffDate,
            'run_cutoff' => $runCutoff,
            'posts_imported' => count($postIdMap),
            'tables_copied' => $tableCounts,
            'cutoff_deleted' => $cutoffCounts,
        ];
        $summary['message'] = sprintf(
            /* translators: 1: source prefix 2: target prefix 3: cutoff date */
            __('Imported WCS4 data from %1$s into %2$s. Cutoff: %3$s.', 'wcs4'),
            $sourcePrefix,
            $wpdb->prefix,
            $cutoffDate
        );
        return $summary;
    }

    private static function clear_wcs4_data_only(): void
    {
        // Truncate plugin tables.
        Schedule::truncate();
        Journal::truncate();
        WorkPlan::truncate();
        Progress::truncate();
        Snapshot::truncate();

        // Delete WCS4 CPT posts (this also removes their term relationships).
        foreach (WCS4_POST_TYPES as $type) {
            $posts = get_posts([
                'numberposts' => -1,
                'post_type' => $type,
                'post_status' => 'any',
                'fields' => 'ids',
            ]);
            foreach ($posts as $postId) {
                wp_delete_post((int)$postId, true);
            }
        }

        // Delete WCS4 taxonomy terms.
        foreach (array_keys(WCS4_TAXONOMY_TYPES_WHITELIST) as $taxonomy) {
            $terms = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'fields' => 'ids',
            ]);
            if (is_wp_error($terms)) {
                continue;
            }
            foreach ($terms as $termId) {
                wp_delete_term((int)$termId, $taxonomy);
            }
        }
    }

    /**
     * Preview how many rows would be affected by a truncate action.
     * @param string $scope schedule|journal|work_plan|progress|snapshot
     */
    public static function preview_truncate(string $scope): array
    {
        global $wpdb;
        switch ($scope) {
            case 'schedule':
                $tables = [
                    Schedule::get_schedule_table_name(),
                    Schedule::get_schedule_teacher_table_name(),
                    Schedule::get_schedule_student_table_name(),
                ];
                break;
            case 'journal':
                $tables = [
                    Journal::get_journal_table_name(),
                    Journal::get_journal_teacher_table_name(),
                    Journal::get_journal_student_table_name(),
                ];
                break;
            case 'work_plan':
                $tables = [
                    WorkPlan::get_work_plan_table_name(),
                    WorkPlan::get_work_plan_teacher_table_name(),
                    WorkPlan::get_work_plan_subject_table_name(),
                ];
                break;
            case 'progress':
                $tables = [
                    Progress::get_progress_table_name(),
                    Progress::get_progress_subject_table_name(),
                    Progress::get_progress_teacher_table_name(),
                ];
                break;
            case 'snapshot':
                $tables = [
                    Snapshot::get_snapshot_table_name(),
                ];
                break;
            default:
                throw new \InvalidArgumentException('Unknown scope.');
        }
        $counts = [];
        $plan = [];
        foreach ($tables as $t) {
            $counts[$t] = (int)$wpdb->get_var("SELECT COUNT(*) FROM $t");
            $plan[] = "TRUNCATE TABLE {$t}; -- would remove {$counts[$t]} rows";
        }
        return [
            'scope' => $scope,
            'tables' => $counts,
            'total_rows' => array_sum($counts),
            'plan' => $plan,
        ];
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function preview_clear_taxonomy(string $taxonomy): array
    {
        if (!array_key_exists($taxonomy, WCS4_TAXONOMY_TYPES_WHITELIST)) {
            throw new \InvalidArgumentException(__('Invalid taxonomy target.', 'wcs4'));
        }
        $terms = get_terms(
            array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'fields' => 'ids',
            )
        );
        $count = is_wp_error($terms) ? 0 : count($terms);
        return array(
            'taxonomy' => $taxonomy,
            'term_count' => $count,
            'plan' => array(
                sprintf(
                    /* translators: 1: number of terms 2: taxonomy slug */
                    __('Delete all %1$d terms in taxonomy “%2$s”.', 'wcs4'),
                    $count,
                    $taxonomy
                ),
            ),
        );
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function clear_taxonomy(string $taxonomy): void
    {
        if (!array_key_exists($taxonomy, WCS4_TAXONOMY_TYPES_WHITELIST)) {
            throw new \InvalidArgumentException(__('Invalid taxonomy target.', 'wcs4'));
        }
        $terms = get_terms(
            array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'fields' => 'ids',
            )
        );
        if (is_wp_error($terms) || empty($terms)) {
            return;
        }
        foreach ($terms as $termId) {
            wp_delete_term((int)$termId, $taxonomy);
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function preview_clear_post_type(string $post_type): array
    {
        if (!in_array($post_type, WCS4_POST_TYPES, true)) {
            throw new \InvalidArgumentException(__('Invalid post type target.', 'wcs4'));
        }
        global $wpdb;
        $count = (int)$wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s", $post_type)
        );
        return array(
            'post_type' => $post_type,
            'post_count' => $count,
            'plan' => array(
                sprintf(
                    /* translators: 1: number of posts 2: post type slug */
                    __('Delete all %1$d posts of type “%2$s” (plugin rows removed via delete_post hook).', 'wcs4'),
                    $count,
                    $post_type
                ),
            ),
        );
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function clear_post_type(string $post_type): void
    {
        if (!in_array($post_type, WCS4_POST_TYPES, true)) {
            throw new \InvalidArgumentException(__('Invalid post type target.', 'wcs4'));
        }
        $posts = get_posts(
            array(
                'posts_per_page' => -1,
                'post_type' => $post_type,
                'post_status' => 'any',
                'fields' => 'ids',
            )
        );
        foreach ($posts as $postId) {
            wp_delete_post((int)$postId, true);
        }
    }

    /**
     * Preview "delete everything" (counts only).
     */
    public static function preview_delete_everything(): array
    {
        global $wpdb;
        $postCounts = [];
        $plan = [];
        foreach (WCS4_POST_TYPES as $type) {
            $postCounts[$type] = (int)$wpdb->get_var(
                $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s", $type)
            );
            $plan[] = "DELETE posts of type {$type}; -- would delete {$postCounts[$type]} posts (and their postmeta/term relationships)";
        }
        $termCounts = [];
        foreach (array_keys(WCS4_TAXONOMY_TYPES_WHITELIST) as $taxonomy) {
            $terms = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'fields' => 'ids',
            ]);
            $termCounts[$taxonomy] = is_wp_error($terms) ? 0 : count($terms);
            $plan[] = "DELETE terms in taxonomy {$taxonomy}; -- would delete {$termCounts[$taxonomy]} terms";
        }
        $plan[] = "DELETE options: wcs4_db_version, wcs4_settings, wcs4_version";
        $tablesToDrop = [
            Schedule::get_schedule_teacher_table_name(),
            Schedule::get_schedule_student_table_name(),
            Schedule::get_schedule_table_name(),
            Journal::get_journal_teacher_table_name(),
            Journal::get_journal_student_table_name(),
            Journal::get_journal_table_name(),
            WorkPlan::get_work_plan_subject_table_name(),
            WorkPlan::get_work_plan_teacher_table_name(),
            WorkPlan::get_work_plan_table_name(),
            Progress::get_progress_subject_table_name(),
            Progress::get_progress_teacher_table_name(),
            Progress::get_progress_table_name(),
            Snapshot::get_snapshot_table_name(),
        ];
        foreach ($tablesToDrop as $t) {
            $plan[] = "DROP TABLE IF EXISTS {$t};";
        }
        return [
            'would_delete_options' => ['wcs4_db_version', 'wcs4_settings', 'wcs4_version'],
            'posts' => $postCounts,
            'taxonomies' => $termCounts,
            'tables' => $tablesToDrop,
            'plan' => $plan,
        ];
    }

    /**
     * Preview import (counts only; no DB changes).
     */
    public static function preview_import_from_prefix(string $sourcePrefix, string $cutoffDate, bool $runCutoff = true): array
    {
        global $wpdb;

        $sourcePrefix = trim($sourcePrefix);
        if ($sourcePrefix === '') {
            throw new \InvalidArgumentException(__('Source prefix is required.', 'wcs4'));
        }
        if (!preg_match('/^[A-Za-z0-9_]+$/', $sourcePrefix) || !str_ends_with($sourcePrefix, '_')) {
            throw new \InvalidArgumentException(__('Source prefix must contain only letters/numbers/underscore and end with underscore (_).', 'wcs4'));
        }
        if ($cutoffDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $cutoffDate)) {
            throw new \InvalidArgumentException(__('Cutoff date must be in YYYY-MM-DD format.', 'wcs4'));
        }
        if ($cutoffDate === '') {
            $cutoffDate = date('Y-m-d');
        }

        $src = [
            'posts' => $sourcePrefix . 'posts',
            'postmeta' => $sourcePrefix . 'postmeta',
            'terms' => $sourcePrefix . 'terms',
            'term_taxonomy' => $sourcePrefix . 'term_taxonomy',
            'term_relationships' => $sourcePrefix . 'term_relationships',
        ];
        $srcWcs = [
            'schedule' => $sourcePrefix . 'wcs4_schedule',
            'schedule_teacher' => $sourcePrefix . 'wcs4_schedule_teacher',
            'schedule_student' => $sourcePrefix . 'wcs4_schedule_student',
            'journal' => $sourcePrefix . 'wcs4_journal',
            'journal_teacher' => $sourcePrefix . 'wcs4_journal_teacher',
            'journal_student' => $sourcePrefix . 'wcs4_journal_student',
            'work_plan' => $sourcePrefix . 'wcs4_work_plan',
            'work_plan_subject' => $sourcePrefix . 'wcs4_work_plan_subject',
            'work_plan_teacher' => $sourcePrefix . 'wcs4_work_plan_teacher',
            'progress' => $sourcePrefix . 'wcs4_progress',
            'progress_subject' => $sourcePrefix . 'wcs4_progress_subject',
            'progress_teacher' => $sourcePrefix . 'wcs4_progress_teacher',
            'snapshot' => $sourcePrefix . 'wcs4_snapshot',
        ];

        $postTypeCounts = [];
        foreach (WCS4_POST_TYPES as $type) {
            $postTypeCounts[$type] = (int)$wpdb->get_var(
                $wpdb->prepare("SELECT COUNT(*) FROM `{$src['posts']}` WHERE post_type = %s", $type)
            );
        }

        $taxonomyCounts = [];
        $taxonomies = array_keys(WCS4_TAXONOMY_TYPES_WHITELIST);
        $placeholders = implode(', ', array_fill(0, count($taxonomies), '%s'));
        $taxonomyCounts = array_fill_keys($taxonomies, 0);
        $taxRows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT taxonomy, COUNT(*) AS cnt
                 FROM `{$src['term_taxonomy']}`
                 WHERE taxonomy IN ($placeholders)
                 GROUP BY taxonomy",
                $taxonomies
            ),
            ARRAY_A
        );
        foreach ($taxRows as $r) {
            $taxonomyCounts[$r['taxonomy']] = (int)$r['cnt'];
        }

        $tableCounts = [];
        foreach ($srcWcs as $k => $t) {
            $tableCounts[$k] = (int)$wpdb->get_var("SELECT COUNT(*) FROM `$t`");
        }

        $cutoffPreview = [];
        if ($runCutoff) {
            $cutoffPreview = [
                'journal' => (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$srcWcs['journal']}` WHERE `date` < %s", $cutoffDate)),
                'work_plan' => (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$srcWcs['work_plan']}` WHERE end_date < %s", $cutoffDate)),
                'progress' => (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$srcWcs['progress']}` WHERE end_date < %s", $cutoffDate)),
                'snapshot' => (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$srcWcs['snapshot']}` WHERE created_at < %s", $cutoffDate . ' 00:00:00')),
            ];
        }

        $plan = [];
        $plan[] = "CLEAR TARGET: truncate wcs4_* tables + delete WCS4 CPT posts + delete WCS4 taxonomy terms (current prefix: {$wpdb->prefix})";
        $plan[] = "IMPORT SOURCE (prefix: {$sourcePrefix}): copy CPT posts (" . implode(', ', WCS4_POST_TYPES) . ") + postmeta";
        $plan[] = "IMPORT SOURCE: copy taxonomies (" . implode(', ', array_keys(WCS4_TAXONOMY_TYPES_WHITELIST)) . ") + term relationships";
        $plan[] = "IMPORT SOURCE: copy wcs4_* tables and remap subject/teacher/student/classroom IDs";
        foreach ($srcWcs as $k => $t) {
            $plan[] = "COPY TABLE {$t} -> {$wpdb->prefix}wcs4_{$k}; -- would copy {$tableCounts[$k]} rows";
        }
        if ($runCutoff) {
            $plan[] = "RUN CUTOFF in TARGET: journal.date < {$cutoffDate}, work_plan/progress end_date < {$cutoffDate}, snapshot.created_at < {$cutoffDate} 00:00:00";
            $plan[] = "CUTOFF PREVIEW (in source counts): journal={$cutoffPreview['journal']}, work_plan={$cutoffPreview['work_plan']}, progress={$cutoffPreview['progress']}, snapshot={$cutoffPreview['snapshot']}";
        } else {
            $plan[] = "SKIP CUTOFF";
        }

        return [
            'source_prefix' => $sourcePrefix,
            'target_prefix' => $wpdb->prefix,
            'cutoff' => $cutoffDate,
            'run_cutoff' => $runCutoff,
            'would_clear_target' => [
                'truncate' => [
                    'schedule' => self::preview_truncate('schedule'),
                    'journal' => self::preview_truncate('journal'),
                    'work_plan' => self::preview_truncate('work_plan'),
                    'progress' => self::preview_truncate('progress'),
                    'snapshot' => self::preview_truncate('snapshot'),
                ],
                'posts' => (static function () {
                    $postCounts = [];
                    foreach (WCS4_POST_TYPES as $type) {
                        $postCounts[$type] = count(get_posts([
                            'numberposts' => -1,
                            'post_type' => $type,
                            'post_status' => 'any',
                            'fields' => 'ids',
                        ]));
                    }
                    return $postCounts;
                })(),
            ],
            'source_counts' => [
                'post_types' => $postTypeCounts,
                'taxonomies' => $taxonomyCounts,
                'tables' => $tableCounts,
            ],
            'would_be_deleted_by_cutoff_in_source' => $cutoffPreview,
            'plan' => $plan,
            'message' => __('Dry run: import preview generated.', 'wcs4'),
        ];
    }

    private static function copy_wcs4_posts_and_meta_from_prefix(string $sourcePrefix): array
    {
        global $wpdb;

        $srcPosts = $sourcePrefix . 'posts';
        $srcPostmeta = $sourcePrefix . 'postmeta';

        $placeholders = implode(', ', array_fill(0, count(WCS4_POST_TYPES), '%s'));
        $query = "SELECT * FROM `$srcPosts` WHERE post_type IN ($placeholders)";
        $rows = $wpdb->get_results($wpdb->prepare($query, WCS4_POST_TYPES), ARRAY_A);

        $postIdMap = [];
        $currentUserId = get_current_user_id();

        foreach ($rows as $row) {
            $oldId = (int)$row['ID'];
            $authorId = (int)$row['post_author'];
            if ($authorId <= 0 || !get_user_by('id', $authorId)) {
                $authorId = $currentUserId;
            }
            $newId = wp_insert_post([
                'post_author' => $authorId,
                'post_date' => $row['post_date'],
                'post_date_gmt' => $row['post_date_gmt'],
                'post_content' => $row['post_content'],
                'post_title' => $row['post_title'],
                'post_excerpt' => $row['post_excerpt'],
                'post_status' => $row['post_status'],
                'comment_status' => $row['comment_status'],
                'ping_status' => $row['ping_status'],
                'post_password' => $row['post_password'],
                'post_name' => $row['post_name'],
                'to_ping' => $row['to_ping'],
                'pinged' => $row['pinged'],
                'post_modified' => $row['post_modified'],
                'post_modified_gmt' => $row['post_modified_gmt'],
                'post_content_filtered' => $row['post_content_filtered'],
                'post_parent' => (int)$row['post_parent'],
                'menu_order' => (int)$row['menu_order'],
                'post_type' => $row['post_type'],
                'post_mime_type' => $row['post_mime_type'],
                'comment_count' => (int)$row['comment_count'],
            ], true);
            if (is_wp_error($newId)) {
                continue;
            }
            $postIdMap[$oldId] = (int)$newId;

            // Copy postmeta.
            $metaRows = $wpdb->get_results(
                $wpdb->prepare("SELECT meta_key, meta_value FROM `$srcPostmeta` WHERE post_id = %d", $oldId),
                ARRAY_A
            );
            foreach ($metaRows as $m) {
                // Do not overwrite (should be empty anyway).
                add_post_meta((int)$newId, $m['meta_key'], $m['meta_value']);
            }
        }

        return $postIdMap;
    }

    private static function copy_wcs4_taxonomies_from_prefix(string $sourcePrefix, array $postIdMap): void
    {
        global $wpdb;

        $srcTerms = $sourcePrefix . 'terms';
        $srcTermTax = $sourcePrefix . 'term_taxonomy';
        $srcRel = $sourcePrefix . 'term_relationships';

        $taxonomies = array_keys(WCS4_TAXONOMY_TYPES_WHITELIST);
        $placeholders = implode(', ', array_fill(0, count($taxonomies), '%s'));

        // Fetch term taxonomy rows + term data for relevant taxonomies.
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT tt.term_taxonomy_id, tt.term_id, tt.taxonomy, tt.description, tt.parent, t.name, t.slug
                 FROM `$srcTermTax` tt
                 INNER JOIN `$srcTerms` t ON t.term_id = tt.term_id
                 WHERE tt.taxonomy IN ($placeholders)
                 ORDER BY tt.parent ASC, tt.term_taxonomy_id ASC",
                $taxonomies
            ),
            ARRAY_A
        );

        // Map old term_id -> new term_id (by insert) and old term_taxonomy_id -> [taxonomy, new_term_id]
        $termIdMap = [];
        $ttMap = [];

        // First pass: insert all terms (ignore parents for now; set later if hierarchical).
        foreach ($rows as $r) {
            $taxonomy = $r['taxonomy'];
            $oldTermId = (int)$r['term_id'];
            $oldTtId = (int)$r['term_taxonomy_id'];

            if (!isset($termIdMap[$oldTermId])) {
                $res = wp_insert_term($r['name'], $taxonomy, [
                    'slug' => $r['slug'],
                    'description' => $r['description'],
                ]);
                if (is_wp_error($res)) {
                    // If already exists, try to find it by slug.
                    $existing = get_term_by('slug', $r['slug'], $taxonomy);
                    if ($existing && !is_wp_error($existing)) {
                        $termIdMap[$oldTermId] = (int)$existing->term_id;
                    }
                } else {
                    $termIdMap[$oldTermId] = (int)$res['term_id'];
                }
            }

            if (isset($termIdMap[$oldTermId])) {
                $ttMap[$oldTtId] = [
                    'taxonomy' => $taxonomy,
                    'term_id' => $termIdMap[$oldTermId],
                ];
            }
        }

        // Second pass: update parent relationships where applicable.
        foreach ($rows as $r) {
            $taxonomy = $r['taxonomy'];
            $oldTermId = (int)$r['term_id'];
            $oldParentTermId = (int)$r['parent'];
            if ($oldParentTermId <= 0) {
                continue;
            }
            if (!isset($termIdMap[$oldTermId], $termIdMap[$oldParentTermId])) {
                continue;
            }
            wp_update_term($termIdMap[$oldTermId], $taxonomy, [
                'parent' => $termIdMap[$oldParentTermId],
            ]);
        }

        // Copy relationships to imported posts.
        $relRows = $wpdb->get_results(
            "SELECT object_id, term_taxonomy_id FROM `$srcRel`",
            ARRAY_A
        );
        foreach ($relRows as $rel) {
            $oldObjectId = (int)$rel['object_id'];
            $oldTtId = (int)$rel['term_taxonomy_id'];
            if (!isset($postIdMap[$oldObjectId], $ttMap[$oldTtId])) {
                continue;
            }
            $newPostId = $postIdMap[$oldObjectId];
            $taxonomy = $ttMap[$oldTtId]['taxonomy'];
            $newTermId = $ttMap[$oldTtId]['term_id'];
            wp_set_object_terms($newPostId, [$newTermId], $taxonomy, true);
        }
    }

    private static function copy_wcs4_tables_from_prefix(string $sourcePrefix, array $postIdMap): array
    {
        global $wpdb;

        $mapItemId = static function ($oldId) use ($postIdMap) {
            $oldId = (int)$oldId;
            return $postIdMap[$oldId] ?? null;
        };

        $counts = [];

        // Schedule
        $counts['schedule'] = self::copy_table_rows(
            $sourcePrefix . 'wcs4_schedule',
            Schedule::get_schedule_table_name(),
            function (array $row) use ($mapItemId) {
                $row['subject_id'] = $mapItemId($row['subject_id']);
                $row['classroom_id'] = $mapItemId($row['classroom_id']);
                if (null === $row['subject_id'] || null === $row['classroom_id']) {
                    return null;
                }
                return $row;
            }
        );
        $counts['schedule_teacher'] = self::copy_table_rows(
            $sourcePrefix . 'wcs4_schedule_teacher',
            Schedule::get_schedule_teacher_table_name(),
            function (array $row) use ($mapItemId) {
                $row['teacher_id'] = $mapItemId($row['teacher_id']);
                if (null === $row['teacher_id']) {
                    return null;
                }
                return $row;
            }
        );
        $counts['schedule_student'] = self::copy_table_rows(
            $sourcePrefix . 'wcs4_schedule_student',
            Schedule::get_schedule_student_table_name(),
            function (array $row) use ($mapItemId) {
                $row['student_id'] = $mapItemId($row['student_id']);
                if (null === $row['student_id']) {
                    return null;
                }
                return $row;
            }
        );

        // Journal
        $counts['journal'] = self::copy_table_rows(
            $sourcePrefix . 'wcs4_journal',
            Journal::get_journal_table_name(),
            function (array $row) use ($mapItemId) {
                $row['subject_id'] = $mapItemId($row['subject_id']);
                if (null === $row['subject_id']) {
                    return null;
                }
                return $row;
            }
        );
        $counts['journal_teacher'] = self::copy_table_rows(
            $sourcePrefix . 'wcs4_journal_teacher',
            Journal::get_journal_teacher_table_name(),
            function (array $row) use ($mapItemId) {
                $row['teacher_id'] = $mapItemId($row['teacher_id']);
                return (null === $row['teacher_id']) ? null : $row;
            }
        );
        $counts['journal_student'] = self::copy_table_rows(
            $sourcePrefix . 'wcs4_journal_student',
            Journal::get_journal_student_table_name(),
            function (array $row) use ($mapItemId) {
                $row['student_id'] = $mapItemId($row['student_id']);
                return (null === $row['student_id']) ? null : $row;
            }
        );

        // Work plan
        $counts['work_plan'] = self::copy_table_rows(
            $sourcePrefix . 'wcs4_work_plan',
            WorkPlan::get_work_plan_table_name(),
            function (array $row) use ($mapItemId) {
                $row['subject_id'] = $mapItemId($row['subject_id']);
                $row['student_id'] = $mapItemId($row['student_id']);
                if (null === $row['subject_id'] || null === $row['student_id']) {
                    return null;
                }
                return $row;
            }
        );
        $counts['work_plan_subject'] = self::copy_table_rows(
            $sourcePrefix . 'wcs4_work_plan_subject',
            WorkPlan::get_work_plan_subject_table_name(),
            function (array $row) use ($mapItemId) {
                $row['subject_id'] = $mapItemId($row['subject_id']);
                return (null === $row['subject_id']) ? null : $row;
            }
        );
        $counts['work_plan_teacher'] = self::copy_table_rows(
            $sourcePrefix . 'wcs4_work_plan_teacher',
            WorkPlan::get_work_plan_teacher_table_name(),
            function (array $row) use ($mapItemId) {
                $row['teacher_id'] = $mapItemId($row['teacher_id']);
                return (null === $row['teacher_id']) ? null : $row;
            }
        );

        // Progress
        $counts['progress'] = self::copy_table_rows(
            $sourcePrefix . 'wcs4_progress',
            Progress::get_progress_table_name(),
            function (array $row) use ($mapItemId) {
                $row['subject_id'] = $mapItemId($row['subject_id']);
                $row['student_id'] = $mapItemId($row['student_id']);
                if (null === $row['subject_id'] || null === $row['student_id']) {
                    return null;
                }
                return $row;
            }
        );
        $counts['progress_subject'] = self::copy_table_rows(
            $sourcePrefix . 'wcs4_progress_subject',
            Progress::get_progress_subject_table_name(),
            function (array $row) use ($mapItemId) {
                $row['subject_id'] = $mapItemId($row['subject_id']);
                return (null === $row['subject_id']) ? null : $row;
            }
        );
        $counts['progress_teacher'] = self::copy_table_rows(
            $sourcePrefix . 'wcs4_progress_teacher',
            Progress::get_progress_teacher_table_name(),
            function (array $row) use ($mapItemId) {
                $row['teacher_id'] = $mapItemId($row['teacher_id']);
                return (null === $row['teacher_id']) ? null : $row;
            }
        );

        // Snapshot (no post FK)
        $counts['snapshot'] = self::copy_table_rows(
            $sourcePrefix . 'wcs4_snapshot',
            Snapshot::get_snapshot_table_name(),
            static fn(array $row) => $row
        );

        return $counts;
    }

    private static function copy_table_rows(string $sourceTable, string $targetTable, callable $transformRow): int
    {
        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM `$sourceTable`", ARRAY_A);
        $inserted = 0;
        foreach ($rows as $row) {
            $row = $transformRow($row);
            if (null === $row) {
                continue;
            }
            // Avoid copying over auto-increment IDs in parent tables.
            // For link tables, `id` is a reference to parent; we keep it as-is (it refers to wcs4_* table row id).
            $ok = $wpdb->insert($targetTable, $row);
            if (false !== $ok) {
                $inserted++;
            }
        }
        return $inserted;
    }

    private static function run_cutoff(string $cutoffDate): array
    {
        global $wpdb;

        $cutoffCounts = [
            'journal' => 0,
            'work_plan' => 0,
            'progress' => 0,
            'snapshot' => 0,
        ];

        // JOURNAL (by date) with cascade to link tables.
        $tableJournal = Journal::get_journal_table_name();
        $tableJournalTeacher = Journal::get_journal_teacher_table_name();
        $tableJournalStudent = Journal::get_journal_student_table_name();

        $ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM $tableJournal WHERE `date` < %s", $cutoffDate));
        if (!empty($ids)) {
            $in = implode(',', array_map('intval', $ids));
            $cutoffCounts['journal'] = count($ids);
            $wpdb->query("DELETE FROM $tableJournalTeacher WHERE id IN ($in)");
            $wpdb->query("DELETE FROM $tableJournalStudent WHERE id IN ($in)");
            $wpdb->query("DELETE FROM $tableJournal WHERE id IN ($in)");
        }

        // WORK PLAN (by end_date) with cascade.
        $tableWp = WorkPlan::get_work_plan_table_name();
        $tableWpTeacher = WorkPlan::get_work_plan_teacher_table_name();
        $tableWpSubject = WorkPlan::get_work_plan_subject_table_name();
        $wpIds = $wpdb->get_col($wpdb->prepare("SELECT id FROM $tableWp WHERE end_date < %s", $cutoffDate));
        if (!empty($wpIds)) {
            $in = implode(',', array_map('intval', $wpIds));
            $cutoffCounts['work_plan'] = count($wpIds);
            $wpdb->query("DELETE FROM $tableWpTeacher WHERE id IN ($in)");
            $wpdb->query("DELETE FROM $tableWpSubject WHERE id IN ($in)");
            $wpdb->query("DELETE FROM $tableWp WHERE id IN ($in)");
        }

        // PROGRESS (by end_date) with cascade.
        $tablePr = Progress::get_progress_table_name();
        $tablePrTeacher = Progress::get_progress_teacher_table_name();
        $tablePrSubject = Progress::get_progress_subject_table_name();
        $prIds = $wpdb->get_col($wpdb->prepare("SELECT id FROM $tablePr WHERE end_date < %s", $cutoffDate));
        if (!empty($prIds)) {
            $in = implode(',', array_map('intval', $prIds));
            $cutoffCounts['progress'] = count($prIds);
            $wpdb->query("DELETE FROM $tablePrTeacher WHERE id IN ($in)");
            $wpdb->query("DELETE FROM $tablePrSubject WHERE id IN ($in)");
            $wpdb->query("DELETE FROM $tablePr WHERE id IN ($in)");
        }

        // SNAPSHOT (by created_at).
        $tableSnap = Snapshot::get_snapshot_table_name();
        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM $tableSnap WHERE created_at < %s",
            $cutoffDate . ' 00:00:00'
        ));
        $cutoffCounts['snapshot'] = (int)$deleted;

        return $cutoffCounts;
    }

    public static function get_item($id): ?Item
    {
        if (empty($id)) {
            return null;
        }
        global $wpdb;
        $table_posts = $wpdb->prefix . 'posts';
        $query = "
            SELECT
              item.ID AS item_id, item.post_title AS item_name, item.post_content AS item_desc
            FROM $table_posts item
            WHERE item.ID = %d
        ";
        $queryArr = [];
        $queryArr[] = str_replace('#', '', $id);
        $query = $wpdb->prepare($query, $queryArr);
        $dbrow = $wpdb->get_row($query);
        if (null === $dbrow) {
            return null;
        }
        return new Item($dbrow->item_id, $dbrow->item_name, $dbrow->item_desc);
    }

    public static function parse_query(array $result): array
    {
        $response = [];
        if ($result) {
            foreach ($result as $key => $val) {
                $response[$key] = (null !== $val && preg_match('/([,]+)/', $val))
                    ? explode(',', $val)
                    : $val;
            }
        }
        return $response;
    }

    public static function get_summary(
        string $query_str,
        array $queryArr = [],
        string $groupBy = 'concat(sub.ID, tea.ID)'
    ): array {
        global $wpdb;
        $summary = [
            (object)[
                'types' => [
                    'group' => 'subject',
                    'row' => 'teacher',
                ],
                'groups' => [],
            ],
            (object)[
                'types' => [
                    'group' => 'teacher',
                    'row' => 'subject',
                ],
                'groups' => [],
            ],
        ];
        foreach ($summary as $filter) {
            $groupKey = $filter->types['group'];
            $groupKeyId = "{$groupKey}_id";
            $groupKeyName = "{$groupKey}_name";
            $rowKey = $filter->types['row'];
            $rowKeyId = "{$rowKey}_id";
            $rowKeyName = "{$rowKey}_name";
            $orderBy = $groupKeyName . ', ' . $rowKeyName;
            $results = $wpdb->get_results(
                $wpdb->prepare($query_str . " GROUP BY $groupBy ORDER BY $orderBy", $queryArr)
            );
            foreach ($results as $row) {
                if (!isset($filter->groups[$row->$groupKeyId])) {
                    $filter->groups[$row->$groupKeyId] = (object)[
                        'id' => $row->$groupKeyId,
                        'name' => $row->$groupKeyName,
                        'rows' => [],
                    ];
                }
                $filter->groups[$row->$groupKeyId]->rows[$row->$rowKeyId] = (object)[
                    'id' => $row->$rowKeyId,
                    'name' => $row->$rowKeyName,
                ];
            }
        }
        return $summary;
    }

    public static function get_items(
        string $class_name,
        string $query_str,
        array $filters,
        array $where,
        array $queryArr,
        array $orderField,
        ?string $limit = null,
        ?int $paged = 1
    ): array {
        global $wpdb;
        foreach ($filters as $filter) {
            $prefix = $filter['prefix'];
            $value = $filter['value'];
            $searchById = $filter['searchById'];
            if (str_starts_with($value, '!')) {
                if (empty($filter['strict'])) {
                    continue;
                }
                $value = preg_replace('/^!/', '', $value);
            } elseif (!empty($filter['strict'])) {
                continue;
            }
            if ('all' !== $value && '' !== $value && null !== $value) {
                if (is_array($value)) {
                    $where[] = $prefix . '.ID IN (' . implode(', ', array_fill(0, count($value), '%s')) . ')';
                    $queryArr += $value;
                } elseif (str_starts_with($value, '#')) {
                    $where[] = $searchById;
                    $queryArr[] = preg_replace('/^#/', '', $value);
                } else {
                    $where[] = $prefix . '.post_title = %s';
                    $queryArr[] = $value;
                }
            }
        }
        if (!empty($where)) {
            $query_str .= ' WHERE ' . implode(' AND ', $where);
        }
        $order = [];
        foreach ($orderField as $field => $direction) {
            $direction = ($direction === 'asc' || $direction === 'ASC') ? 'ASC' : 'DESC';
            $order[] = sprintf('%s %s', $field, $direction);
        }
        $query_str .= ' ORDER BY ' . implode(', ', $order);
        if (null !== $limit) {
            $query_str .= ' LIMIT %d';
            $queryArr[] = $limit;
            if (null !== $paged) {
                $query_str .= ' OFFSET %d';
                $queryArr[] = $limit * ($paged - 1);
            }
        }
        $query = $wpdb->prepare($query_str, $queryArr);
        if (isset($_GET['debug'])) {
            dump($query);
        }
        $results = $wpdb->get_results($query);
        $format = get_option('time_format');
        $items = [];
        if ($results) {
            foreach ($results as $row) {
                $item = new $class_name($row, $format);
                $item = apply_filters('wcs4_format_class', $item);
                if (!isset($items[$item->getId()])) {
                    $items[$item->getId()] = $item;
                } else {
                    switch ($class_name) {
                        case Lesson_Item::class:
                            /** @var Lesson_Item $_item */
                            $_item = $items[$item->getId()];
                            $_item->addTeachers($item->getTeachers());
                            $_item->addStudents($item->getStudents());
                            break;
                        case Journal_Item::class:
                            /** @var Journal_Item $_item */
                            $_item = $items[$item->getId()];
                            $_item->addTeachers($item->getTeachers());
                            $_item->addStudents($item->getStudents());
                            break;
                        case WorkPlan_Item::class:
                            /** @var WorkPlan_Item $_item */
                            $_item = $items[$item->getId()];
                            $_item->addSubjects($item->getSubjects());
                            $_item->addTeachers($item->getTeachers());
                            break;
                        case Progress_Item::class:
                            /** @var Progress_Item $_item */
                            $_item = $items[$item->getId()];
                            $_item->addSubjects($item->getSubjects());
                            $_item->addTeachers($item->getTeachers());
                            break;
                    }
                }
            }
        }
        return $items;
    }

}
