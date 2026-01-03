"""Run migration to add option_parts table."""
import sqlite3
import sys
from pathlib import Path

# Database path
db_path = Path(__file__).parent.parent / "data" / "kumpe3d.db"

print(f"Running migration on {db_path}")

# Read migration SQL
migration_sql = (Path(__file__).parent / "003_add_option_parts.sql").read_text()

# Connect and execute
conn = sqlite3.connect(db_path)
try:
    conn.executescript(migration_sql)
    conn.commit()
    print("✅ Migration completed successfully")
except Exception as e:
    conn.rollback()
    print(f"❌ Migration failed: {e}")
    sys.exit(1)
finally:
    conn.close()
