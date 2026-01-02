import asyncio
import sys
import os
sys.path.insert(0, '/app')
os.chdir('/app')

from sqlalchemy import select
from sqlalchemy.orm import selectinload
from db.database import async_session_maker
from db.models import Product, ProductPart

async def test():
    async with async_session_maker() as session:
        query = select(Product).where(Product.id == 1)
        query = query.options(
            selectinload(Product.parts).selectinload(ProductPart.part)
        )
        result = await session.execute(query)
        product = result.scalar_one_or_none()
        
        print(f'Product: {product.title}')
        print(f'Parts loaded: {len(product.parts)}')
        print(f'Allow order when out of stock: {product.allow_order_when_out_of_stock}')
        print(f'Stock quantity property: {product.stock_quantity}')
        
        print('\nFirst 3 parts:')
        for pp in product.parts[:3]:
            print(f'  ProductPart {pp.part_id}:')
            print(f'    quantity required: {pp.quantity}')
            print(f'    is_optional: {pp.is_optional}')
            print(f'    alternative_group: {pp.alternative_group}')
            print(f'    part object loaded: {pp.part is not None}')
            if pp.part:
                print(f'    part.stock_quantity: {pp.part.stock_quantity}')

asyncio.run(test())
