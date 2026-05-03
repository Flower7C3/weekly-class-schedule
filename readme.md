## Weekly Class Schedule and Journal

- Contributors: flower7c3, ty_pwd
- Tags: schedule, weekly schedule, journal, subjects, teachers, students, classes, lessons
- Requires at least: 4.0
- Tested up to: 5.8
- License: GPLv2 or later

Generate a weekly schedule of subjects.

### Description

Weekly Class Schedule generates a weekly schedule of subjects using an ultra-simple interface.

### Main Features

* Easily manage and update schedule entries (subjects).
* Manage and update the subjects, teachers, and classrooms (classrooms) database.
* Easy customization of schedule appearance and colors.
* Includes "Today's Classes" widget.
* Use simple shortcode attributes to switch between standard and list layout.
* Use a simple templating system to customize the class details display.
* Supports multiple classrooms/schedules/teachers/students.
* Switchable "Teacher collision detection", "Student collision detection" and "Classroom collision detection".
* Display class and teacher details directly on the schedule using qTip2.
* Allow to add journals and progresses.

### Front-end CSS variables

The plugin stylesheet `css/wcs_front.css` defines custom properties on `:root`. You can reuse them in child themes, `Additional CSS`, or companion plugins (e.g. `border-color: var(--wcs4-ui-border);`).

| Variable | Role |
|----------|------|
| `--wcs4-ui-accent-deep` | Darkest accent (headings, strong emphasis) |
| `--wcs4-ui-accent` | Primary accent |
| `--wcs4-ui-accent-mid` | Mid accent (gradients, mid tones) |
| `--wcs4-ui-accent-bright` | Lighter accent (hover states) |
| `--wcs4-ui-on-accent` | Text/icons on top of accent backgrounds |
| `--wcs4-ui-surface` | Main surface / card background |
| `--wcs4-ui-surface-muted` | Muted surface (panels, banners) |
| `--wcs4-ui-surface-tint` | Slight blue tint for layered surfaces |
| `--wcs4-ui-text` | Primary body text on surfaces |
| `--wcs4-ui-text-soft` | Secondary / softer text |
| `--wcs4-ui-border` | Default borders |
| `--wcs4-ui-border-strong` | Stronger borders (controls, chips) |
| `--wcs4-ui-shadow` | Default elevation shadow |
| `--wcs4-ui-shadow-hover` | Stronger shadow (hover) |
| `--wcs4-ui-focus` | Focus ring color |
| `--wcs4-ui-footer-tint` | Subtle footer / bar tint |
| `--wcs4-ui-radius-sm` | Small border radius |
| `--wcs4-ui-radius-md` | Medium border radius (modals, cards) |
| `--wcs4-ui-backdrop` | Modal backdrop (legacy fallback; modal overlay may use theme `color-mix`) |
| `--wcs4-ui-modal-shadow` | Modal / elevated panel shadow |
| `--wcs4-ui-success` | Success message text |
| `--wcs4-ui-success-bg` | Success message background |
| `--wcs4-ui-danger` | Error message text |
| `--wcs4-ui-danger-bg` | Error message background |

**Block themes (e.g. Twenty Twenty-Five):** schedule and UI pieces often use WordPress preset variables first, with the table above as fallback, for example:

- `--wp--preset--color--base`, `--wp--preset--color--contrast`, `--wp--preset--color--accent-1` … `--wp--preset--color--accent-6` (exact slugs depend on the theme `theme.json` palette)
- `--wp--preset--spacing--20`, `--wp--preset--spacing--50`, … (spacing scale from the theme)
- `--wp--style--block-gap` (gap between blocks in flex layouts)

See [Global Settings & Styles (theme.json)](https://developer.wordpress.org/themes/global-settings-and-styles/) for how presets map to CSS.

### Installation

1. Upload the entire `weekly-class-schedule` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

You will find a 'Schedule' menu item as well as 'Classes', 'Teachers', 'Students', and 'Classrooms' in your WordPress
admin panel.

### Screenshots

1. Schedule Management
2. Color Customization
3. Standard Layout
4. List Layout

### Changelog

[commits/master](https://github.com/Flower7C3/weekly-class-schedule/commits/master)
