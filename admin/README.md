# iVotePH Admin Module

A complete PHP + MySQL + Bootstrap 5 admin module for the simulated Philippine online voting platform **iVotePH**. This is designed for academic/demo use and can be connected to an existing voter-facing website.

## Default test credentials

Admin login URL: `auth/login.php`

- Email: `admin@ivoteph.test`
- Password: `Admin@12345`

Sample voter account password in `accounts`: `Voter@123`

## Folder structure

```text
ivoteph-admin/
├── admin/
│   ├── index.php
│   ├── voters.php
│   ├── candidates.php
│   ├── positions.php
│   ├── elections.php
│   ├── results.php
│   └── audit_logs.php
├── assets/
│   ├── css/admin.css
│   ├── js/admin.js
│   └── uploads/candidates/.gitkeep
├── auth/
│   ├── login.php
│   └── logout.php
├── config/
│   └── config.php
├── helpers/
│   └── functions.php
├── includes/
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
├── sql/
│   ├── ivoteph_schema.sql
│   └── ivoteph_erd_and_3nf.md
└── index.php
```

## Installation

1. In WAMP, copy this folder to `C:\wamp64\www\ivoteph-admin`.
2. In MySQL Workbench, open and run `sql/ivoteph_schema.sql`. It creates the `ivoteph` database, tables, constraints, triggers, and sample records.
3. Edit database settings in `config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'ivoteph');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('BASE_URL', '');
   ```
4. Make sure WAMP is green, then open `http://localhost/ivoteph-admin/`.
5. Make sure `assets/uploads/candidates` is writable.
6. Open `auth/login.php` and log in with the test credentials above.


## Voter registry and account logic

The database follows a COMELEC-style master list flow:

- `registered_voters` stores eligible voters imported by the administrator.
- `accounts` stores voter login accounts.
- `accounts.voter_id` is unique, so one voter can only create one account.
- Account creation automatically updates `registered_voters.registration_status` to `Registered` through a database trigger.
- `votes.voter_id` references `accounts.voter_id`, so only voters with accounts can vote.
- `votes` has `UNIQUE(election_id, voter_id, position_id)`, so one voter can vote only once per position per election.
- Vote update/delete is blocked by triggers to preserve ballot finality.

See `sql/ivoteph_erd_and_3nf.md` for the ERD structure, 3NF explanation, and user-side registration/login query patterns.

## Connecting to your existing voter website

Use the same database. The admin module manages these shared tables:

- `registered_voters`
- `accounts`
- `positions`
- `candidates`
- `elections`
- `votes`

Your voter-facing site can read `elections`, `positions`, and `candidates`, then insert into `votes` using the unique rule `UNIQUE(election_id, voter_id, position_id)` to prevent duplicate voting per position.

## Security included

- PDO prepared statements
- Password hashing with `password_hash()` / `password_verify()`
- Session-only admin access
- CSRF tokens for write actions
- Server-side validation
- Image upload validation
- Audit logs for login, logout, add, update, delete, and election status actions

## Academic note

This is a simulation system for coursework. For real public elections, online voting requires legal authorization, independent security audits, end-to-end verifiability, strong identity proofing, threat modeling, and strict compliance with election law.
