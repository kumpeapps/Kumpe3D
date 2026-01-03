"""add order item fulfillment tracking

Revision ID: 004
Revises: 003
Create Date: 2026-01-03 18:30:00.000000

"""
from alembic import op
import sqlalchemy as sa
from sqlalchemy.dialects import sqlite

# revision identifiers, used by Alembic.
revision = '004'
down_revision = '003'
branch_labels = None
depends_on = None


def upgrade() -> None:
    """Add fulfillment tracking fields to order_items."""
    
    # Add new columns to order_items
    with op.batch_alter_table('order_items', schema=None) as batch_op:
        batch_op.add_column(sa.Column('is_filled', sa.Boolean(), nullable=False, server_default='0'))
        batch_op.add_column(sa.Column('filled_at', sa.DateTime(), nullable=True))
        batch_op.add_column(sa.Column('filled_by', sa.String(100), nullable=True))
        batch_op.add_column(sa.Column('scanned_parts', sa.Text(), nullable=True))  # JSON array of scanned part IDs
        batch_op.add_column(sa.Column('notes', sa.Text(), nullable=True))
    
    # Create order_item_parts junction table to track which parts are needed for each order item
    op.create_table(
        'order_item_parts',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('order_item_id', sa.Integer(), nullable=False),
        sa.Column('part_id', sa.Integer(), nullable=False),
        sa.Column('quantity', sa.Integer(), nullable=False),
        sa.Column('alternative_group', sa.Integer(), nullable=True),
        sa.Column('alternative_part_ids', sa.Text(), nullable=True),
        sa.Column('is_scanned', sa.Boolean(), nullable=False, server_default='0'),
        sa.Column('scanned_at', sa.DateTime(), nullable=True),
        sa.Column('scanned_by', sa.String(100), nullable=True),
        sa.Column('actual_part_id', sa.Integer(), nullable=True),
        sa.Column('created_at', sa.DateTime(), nullable=False, server_default=sa.text('CURRENT_TIMESTAMP')),
        sa.ForeignKeyConstraint(['order_item_id'], ['order_items.id'], ondelete='CASCADE'),
        sa.ForeignKeyConstraint(['part_id'], ['parts.id'], ondelete='RESTRICT'),
        sa.ForeignKeyConstraint(['actual_part_id'], ['parts.id'], ondelete='RESTRICT'),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('order_item_id', 'part_id', name='uq_order_item_part')
    )
    
    # Create indexes
    with op.batch_alter_table('order_item_parts', schema=None) as batch_op:
        batch_op.create_index('ix_order_item_parts_order_item_id', ['order_item_id'])
        batch_op.create_index('ix_order_item_parts_part_id', ['part_id'])
        batch_op.create_index('ix_order_item_parts_is_scanned', ['is_scanned'])


def downgrade() -> None:
    """Remove fulfillment tracking."""
    
    # Drop order_item_parts table
    op.drop_table('order_item_parts')
    
    # Remove columns from order_items
    with op.batch_alter_table('order_items', schema=None) as batch_op:
        batch_op.drop_column('notes')
        batch_op.drop_column('scanned_parts')
        batch_op.drop_column('filled_by')
        batch_op.drop_column('filled_at')
        batch_op.drop_column('is_filled')
