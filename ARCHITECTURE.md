# hypeInvite — Plugin Architecture (Elgg 7.x)

An interface for inviting new users and managing group invitations on Elgg sites.

## Plugin ID

`hypeinvite`

## Entity Types

| Type | Subtype | Class | Description |
|------|---------|-------|-------------|
| `object` | `user_invite` | `Invite` | An invitation to the site for a specific email |
| `object` | `group_invite` | `Invite` | A group invitation for a specific email |
| `object` | `user_invite_request` | `InviteRequest` | A user's request to be invited |

## Directory Structure

```
hypeinvite/
├── classes/hypeJunction/Invite/
│   ├── Bootstrap.php                      # Plugin bootstrap (conditional hypeHero menu wiring)
│   ├── Invite.php                         # ElggObject subclass for user/group invites
│   ├── InviteRequest.php                  # ElggObject subclass for invite requests
│   ├── InviteService.php                  # Business logic: create/validate/get invites
│   ├── AcceptFriendInvitesOnRegistration.php  # Event: accept:invite → create friendships
│   ├── AcceptGroupInvitesOnRegistration.php   # Event: accept:invite → join groups
│   ├── ProcessUserInvitesOnRegistration.php   # Event: create:user → process pending invites
│   ├── RegistrationForwardUrl.php         # Event: register:user → override forward URL
│   ├── GenerateRegistrationUrl.php        # Event: registration_url:site → embed invite code
│   ├── EntityMenu.php                     # Event: register:menu:entity → group invite menu item
│   ├── GroupTitleMenu.php                 # Event: register:menu:title → remove group invite item
│   ├── PageMenu.php                       # Event: register:menu:page → page menu items
│   ├── RegistrationMiddleware.php         # Route middleware: validate invite code
│   ├── ConfirmGroupInvite.php             # Route controller: confirm group invitation
│   ├── ConfirmInviteAction.php            # Admin action: approve invite request
│   ├── InviteGroupMembersAction.php       # Action: invite users to a group
│   ├── InviteUsersAction.php              # Action: invite users to the site
│   ├── RequestInviteAction.php            # Public action: request an invitation
│   └── Seeder.php                         # Elgg\Database\Seeds\Seed subclass — seeds user_invite objects
├── lib/
│   └── functions.php                      # Legacy helper functions (used in views/plugins)
├── views/
│   └── default/
│       ├── admin/users/requests.mjs       # ES module — admin invite-request table select-all
│       ├── object/user_invite_request/actions.mjs  # ES module — AJAX confirm/delete invite request
│       ├── forms/                         # Invite forms (friends/invite, groups/invite)
│       ├── object/user_invite/            # Invite object views
│       ├── object/user_invite_request/    # Invite request views
│       ├── plugins/hypeInvite/            # Plugin settings view
│       └── resources/                     # Page resources (friends/invite, groups/invite)
├── languages/                             # Translation files
├── docker/                                # Per-plugin Elgg 7.x Docker stack
├── elgg-plugin.php                        # Declarative plugin config
├── elgg-services.php                      # DI service registration (users.invites)
└── composer.json                          # php >=8.3, ext-intl, elgg/elgg ~7.0.0
```

## Events Registered

All registered via `'events'` key in `elgg-plugin.php`:

| Event | Type | Handler | Purpose |
|-------|------|---------|---------|
| `registration_url` | `site` | `GenerateRegistrationUrl` | Embed invite code in registration URL |
| `register` | `user` | `RegistrationForwardUrl` | Redirect to context (group/friend) after registration |
| `register` | `menu:page` | `PageMenu` | Add invite/admin menu items |
| `register` | `menu:entity` | `EntityMenu` | Add group invite menu item |
| `accept` | `invite` | `AcceptFriendInvitesOnRegistration` | Create friend relationships |
| `accept` | `invite` | `AcceptGroupInvitesOnRegistration` | Join groups on invite acceptance |
| `create` | `user` | `ProcessUserInvitesOnRegistration` | Process pending invites on new user creation |

Plus, via `Bootstrap::init()` when hypeHero is active:
- `register:menu:title` → `GroupTitleMenu` (removes redundant group invite item)
- `register:menu:actions` → `GroupTitleMenu`

## Routes

| Name | Path | Handler | Access |
|------|------|---------|--------|
| `account:register` | `/register` | `account/register` resource | Public (walled: false), `RegistrationMiddleware` |
| `friends:invite` | `/friends/{username?}/invite` | `friends/invite` resource | Auth + `UserPageOwnerGatekeeper` |
| `invite:request` | `/users/request_invitation` | resource | Public (walled: false) |
| `invite:group:group:confirm` | `/groups/confirm_invitation` | `ConfirmGroupInvite` | Public (walled: false) |

## Actions

| Action | Controller | Access |
|--------|-----------|--------|
| `users/request_invitation` | `RequestInviteAction` | Public |
| `users/confirm_invitation` | `ConfirmInviteAction` | Admin |
| `users/invite` | `InviteUsersAction` | Logged-in |
| `groups/invite` | `InviteGroupMembersAction` | Logged-in |

## Group Tools

- `invites` — default off; when enabled shows invite menu in group entity menu

## Dependencies

No required plugin dependencies declared. Optional integration:
- `friend_request` — if active, creates `friendrequest` relationships instead of direct friendships
- `groups` — if active, redirects to group invitations page on registration
- `hypeHero` — if active, removes duplicate menu item from hero title/actions menu

## Services (DI)

- `users.invites` → `InviteService` — registered in `elgg-services.php`

## Seeding

`hypeJunction\Invite\Seeder` extends `\Elgg\Database\Seeds\Seed` and is wired
to the `seeds, database` event in `Bootstrap::init()`. It seeds `user_invite`
objects tagged with `__faker` metadata so `elgg-cli database:unseed` removes
them cleanly. The plugin owns entity subtypes, so a seeder is mandatory.

## JavaScript

Elgg 6.x+ uses native ES modules (RequireJS/AMD removed). The plugin ships
two `.mjs` modules:

- `views/default/admin/users/requests.mjs` — imported via `elgg_import_esm()`
  in `views/default/admin/users/requests.php`
- `views/default/object/user_invite_request/actions.mjs` — referenced as a
  menu-item `'deps'` entry in `views/default/object/user_invite_request/actions.php`

## Migration Notes (6.x → 7.x)

- `composer.json`: `php` `>=8.2` → `>=8.3`, `elgg/elgg` `~6.1.0` → `~7.0.0`
- `composer.json`: added the Elgg 7.x-required stability settings
  (`minimum-stability: dev`, `prefer-stable: true`, asset-packagist repository)
  via the automated `composer-stability-settings-7x` rule
- Docker infra upgraded to the Elgg 7.x stack (`php:8.3-apache`,
  `elgg/elgg ~7.0.0`, PHPUnit `^10.5 || ^11.0`)
- No code changes required — the plugin uses none of the APIs removed or
  changed in Elgg 7.x: no `new ElggObject` (it uses the `Invite` /
  `InviteRequest` subclasses), no CSS Crush syntax / `.css` files, no
  Redis/Memcache config, no `Laminas\Mail`, no renamed notification handler
  classes, no `ajax_response`/`forward` events, no removed button classes,
  no `subpage` group routes, no `flush_cache`, no renamed members routes,
  no messages `recipients` param, no external-pages integration, no custom
  password validation, no river-emitting plugin entities, no CKEditor
  customization, no likes integration, no web services
- `elgg_list_entities` calls that need unlimited results already pass
  `'limit' => 0` directly in the options array (not from a query string),
  so the 7.x query-string limit clamping does not affect them
- PHPUnit suite (34 tests) runs unchanged on PHPUnit 10.5 — no deprecated
  annotations or assertion methods in use

## Migration Notes (5.x → 6.x)

- `composer.json`: `elgg/elgg` `^5.0` → `~6.1.0`, added `ext-intl`, dropped the
  hardcoded `version` field (resolved from git tags)
- AMD → ES modules: `requests.js`/`actions.js` converted to `.mjs` with native
  `import` syntax; `elgg_require_js()` → `elgg_import_esm()` (the AMD functions
  `elgg_require_js`/`elgg_define_js` were removed in Elgg 6.x)
- Docker infra upgraded to the Elgg 6.x stack (`elgg/elgg ~6.1.0`, PHPUnit 10.5)
- `Seeder` repaired during the 5.x baseline pass (it was missing the
  `getType()`/`getCountOptions()` abstract methods required since Elgg 5.x,
  which caused a fatal on activation)
- No annotation/icon/hook-function/raw-SQL/CSS changes were needed — the plugin
  uses none of the APIs removed by the other 5x→6x breaking changes
- PHPUnit suite (34 tests) runs unchanged on PHPUnit 10.5 — no deprecated
  annotations or assertion methods in use

### Known pre-existing issue (not introduced by this migration)

All `elgg_get_plugin_setting(..., 'hypeInvite', ...)` callsites use the
camelCase plugin id `'hypeInvite'` instead of the lowercase `'hypeinvite'`.
Since Elgg 4.x, `elgg_get_plugin_setting()` silently returns `false` on a
camelCase id rather than the stored value or the supplied default. This is a
latent 4.x-era defect present on the 5.x base and carried forward unchanged;
it should be fixed in a dedicated pass (it is out of scope for the 5→6
version migration and is not a regression).
