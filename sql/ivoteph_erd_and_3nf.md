# iVotePH Database Design, ERD Structure, and 3NF Notes

## Core Concept

iVotePH uses a COMELEC-style master voter registry. The `registered_voters` table is imported by the administrator before an election starts. A user can only create an account when the voter identity details match one record in `registered_voters`.

## Account Registration Logic

1. User enters `voter_id`, `first_name`, `last_name`, and `birth_date`.
2. The user-side PHP code searches `registered_voters` for an exact match.
3. If no record exists, show: `Voter record not found.`
4. If an account already exists in `accounts` for that `voter_id`, prevent duplicate registration and direct the user to login.
5. If the voter exists and has no account, insert into `accounts` using `password_hash()`.
6. The database trigger automatically marks `registered_voters.registration_status` as `Registered`.

## Login Logic

Users may log in using either `voter_id` or `username` plus password. The user-side login query should join `accounts` and `registered_voters`, then validate `password_hash` with PHP `password_verify()`.

## ERD Structure

```text
admins
  admin_id PK
  admin_name
  email UNIQUE
  password_hash
  created_at

registered_voters
  voter_id PK
  first_name
  last_name
  birth_date
  email UNIQUE
  registration_status
  created_at
  updated_at

accounts
  account_id PK
  voter_id FK UNIQUE -> registered_voters.voter_id
  username UNIQUE
  password_hash
  is_active
  created_at

positions
  position_id PK
  position_name UNIQUE
  description
  max_votes
  display_order
  created_at
  updated_at

candidates
  candidate_id PK
  full_name
  political_party
  position_id FK -> positions.position_id
  photo
  platform
  created_at
  updated_at

elections
  election_id PK
  election_title
  start_date
  end_date
  status
  created_at
  updated_at

votes
  vote_id PK
  election_id FK -> elections.election_id
  voter_id FK -> accounts.voter_id
  candidate_id + position_id FK -> candidates.candidate_id + candidates.position_id
  position_id FK -> positions.position_id
  vote_timestamp
  UNIQUE(election_id, voter_id, position_id)

audit_logs
  log_id PK
  admin_id FK -> admins.admin_id
  admin_name
  action
  created_at
```

## Relationship Cardinality

```text
registered_voters 1 ─── 0..1 accounts
positions          1 ─── 0..N candidates
elections          1 ─── 0..N votes
accounts           1 ─── 0..N votes
positions          1 ─── 0..N votes
candidates         1 ─── 0..N votes
admins             1 ─── 0..N audit_logs
```

## Business Rules Implemented in the Schema

| Rule | Implementation |
|---|---|
| `voter_id` must be unique | `registered_voters.voter_id` primary key |
| One voter can only have one account | `accounts.voter_id` unique foreign key |
| A voter cannot create an account unless they exist in the master list | `accounts.voter_id` foreign key + trigger message |
| Voter is marked registered after account creation | `trg_accounts_after_insert` trigger |
| One voter can vote only once per position | `UNIQUE(election_id, voter_id, position_id)` in `votes` |
| Candidates must belong to a valid position | `candidates.position_id` foreign key |
| Votes must reference a valid candidate and matching position | Composite FK `(candidate_id, position_id)` |
| Votes cannot be edited or deleted | `trg_votes_no_update` and `trg_votes_no_delete` triggers |
| Voting must be open | `trg_votes_before_insert` checks election status |
| Only active registered account holders can vote | `votes.voter_id` references `accounts.voter_id` and trigger checks `is_active` |

## 3NF Normalization Notes

The design is in Third Normal Form because:

1. **1NF:** Each table has atomic fields. There are no repeating groups such as `candidate1`, `candidate2`, or multiple vote columns in one voter record.
2. **2NF:** Non-key attributes depend on the full primary key of their table. For example, candidate details are stored in `candidates`, not repeated inside `votes`.
3. **3NF:** Non-key attributes do not depend on other non-key attributes. For example, `position_name` is stored only in `positions`, while `candidates` stores only `position_id` as a reference.

## User-Side Registration Query Pattern

```sql
SELECT voter_id, first_name, last_name, birth_date, registration_status
FROM registered_voters
WHERE voter_id = :voter_id
  AND first_name = :first_name
  AND last_name = :last_name
  AND birth_date = :birth_date
LIMIT 1;
```

Then check duplicate account:

```sql
SELECT account_id
FROM accounts
WHERE voter_id = :voter_id
LIMIT 1;
```

Then create account:

```sql
INSERT INTO accounts (voter_id, username, password_hash)
VALUES (:voter_id, :username, :password_hash);
```

## User-Side Login Query Pattern

```sql
SELECT a.account_id, a.voter_id, a.username, a.password_hash, a.is_active,
       rv.first_name, rv.last_name, rv.registration_status
FROM accounts a
INNER JOIN registered_voters rv ON rv.voter_id = a.voter_id
WHERE a.username = :login OR a.voter_id = :login
LIMIT 1;
```

Use PHP `password_verify($password, $row['password_hash'])` after fetching the row.
