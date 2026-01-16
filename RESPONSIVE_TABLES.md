# Responsive Data Table System - Documentation

## Overview
This document explains how to implement responsive mobile-friendly data tables in your application.

## Files Modified
- `resources/css/responsive-tables.css` - Main responsive table styles
- `resources/css/app.css` - Updated to import responsive-tables.css
- Updated views: properties, rentals, loans, finance, customers

## How It Works

### 1. Mobile View (< 768px)
- Table headers (`<thead>`) are hidden
- Table rows are converted to cards with:
  - Border and border-radius
  - Box shadow for depth
  - Margin between cards
  - Column labels displayed as `::before` pseudo-elements

### 2. Desktop View (≥ 768px)
- Standard table layout is preserved
- All styling reverts to original table appearance

## How to Add to New Tables

### Step 1: HTML Structure
Wrap your table in a `.table-responsive` div:

```blade
<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead class="table-dark">
            <!-- Your headers -->
        </thead>
        <tbody>
            <!-- Your rows -->
        </tbody>
    </table>
</div>
```

### Step 2: Add `data-label` Attributes
Add `data-label="ชื่อคอลัมน์"` to each `<td>` element:

```blade
<tr>
    <td data-label="รหัส">
        <span class="badge bg-primary">{{ $item->code }}</span>
    </td>
    <td data-label="ชื่อ">
        <strong>{{ $item->name }}</strong>
    </td>
    <td data-label="ราคา">
        {{ number_format($item->price, 2) }} ฿
    </td>
    <td data-label="การดำเนินการ">
        <!-- Action buttons -->
    </td>
</tr>
```

### Step 3: CSS Customization (Optional)

If you need custom styling for specific columns, you can add:

```css
@media (max-width: 767.98px) {
    /* Custom styles for your specific data-label */
    .table-responsive tbody td[data-label="ราคา"] {
        /* Custom styles */
    }
}
```

## Features

### Automatic Label Display
- Labels are pulled from `data-label` attributes using CSS `content: attr(data-label)`
- Labels appear in bold on the left side
- Data is aligned to the right for easy reading

### Action Buttons
- Automatically wrap to full width on mobile
- Stacked with proper spacing
- Maintains functionality on touch devices

### Status Badges
- Properly displayed on mobile with color coding
- Aligned to the right for easy identification

### Price/Amount Display
- Right-aligned for better readability
- Works with or without currency symbols

## Browser Support
- All modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile browsers (iOS Safari, Chrome Mobile)
- IE 11+ (with graceful degradation)

## Responsive Breakpoints
- **Mobile**: < 768px (card layout with labels)
- **Tablet**: 768px - 991px (table with optimized columns)
- **Desktop**: ≥ 992px (full table layout)

## Tips for Best Results

1. **Keep Table Headers Simple**: Use short, clear labels in both `<th>` and `data-label`
2. **Limit Visible Columns on Mobile**: Consider hiding less important columns on small screens
3. **Test on Real Devices**: Always test the responsive behavior on actual mobile devices
4. **Consider Font Sizes**: Ensure text is readable on small screens
5. **Action Buttons**: Keep button text short (e.g., ✏️ rather than "Edit Full Text")

## Example - Complete Table

```blade
<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th>รหัส</th>
                <th>ชื่อสินค้า</th>
                <th>ราคา</th>
                <th>สถานะ</th>
                <th>การดำเนินการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td data-label="รหัส">
                        <span class="badge bg-primary">{{ $product->code }}</span>
                    </td>
                    <td data-label="ชื่อสินค้า">
                        <strong>{{ $product->name }}</strong>
                    </td>
                    <td data-label="ราคา">
                        <strong>{{ number_format($product->price, 2) }} ฿</strong>
                    </td>
                    <td data-label="สถานะ">
                        <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td data-label="การดำเนินการ">
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">
                            ✏️ Edit
                        </a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" 
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Confirm delete?')">
                                🗑️ Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

## Troubleshooting

### Labels not showing on mobile?
- Ensure `data-label` attributes are present on all `<td>` elements
- Clear browser cache
- Check browser DevTools to confirm media query is active

### Buttons looking weird on mobile?
- Check button container width
- Ensure buttons have proper `btn` classes
- Consider using icon-only buttons for space efficiency

### Text overlapping?
- Increase padding in CSS
- Use shorter labels
- Break long text using `<br>` tags

## CSS Customization Examples

### Hide column on mobile
```css
@media (max-width: 767.98px) {
    .table-responsive tbody td[data-label="ที่ไม่ต้องแสดง"]::before {
        display: none;
    }
    
    .table-responsive tbody td[data-label="ที่ไม่ต้องแสดง"] {
        display: none;
    }
}
```

### Custom layout for specific data-label
```css
@media (max-width: 767.98px) {
    .table-responsive tbody td[data-label="รหัส"] {
        flex-direction: row;
        align-items: center;
    }
}
```

## Performance Notes
- CSS-only solution: no JavaScript required
- Minimal performance impact
- Uses CSS media queries for responsive behavior
- Compatible with Bootstrap utilities

---

Last Updated: January 17, 2026
