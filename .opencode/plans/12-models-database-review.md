# Models, Relationships & Database Review

## Scope
All 16 models for relationship correctness, index optimization, data integrity constraints, and schema design.

## Areas to Cover
- `app/Models/` — All 16 models
- `database/migrations/` — All 21 migrations
- `database/factories/` — All 14 factories

## Review Prompts
1. **Relationship correctness**: Are all relationships properly defined (belongsTo vs hasMany, foreign keys, correct model)?
2. **Missing indexes**: Are foreign key columns and frequently queried columns indexed?
3. **Cascade deletes**: Are onDelete behaviors correct? Should some be `SET NULL` instead of `CASCADE`?
4. **Unique constraints**: Are there proper unique indexes (e.g., booking reference, visa per client+package)?
5. **Nullable fields**: Are optional columns correctly nullable? Are required columns properly constrained?
6. **Cast correctness**: Are JSON fields cast? Are boolean/timestamp casts correct?
7. **Soft delete relationships**: Do all queries properly handle soft-deleted related records?
8. **Factory accuracy**: Do factories produce valid data matching schema constraints?
9. **Migration ordering**: Are foreign keys added after the referenced table is created?
10. **Eager loading**: Are there any N+1 query risks in relationships?
