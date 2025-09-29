# 🎨 Project Color Guide

This document defines the consistent color scheme used throughout the Laravel Excel Management System.

## 🎯 Primary Color Palette

### Main Brand Colors
- **Primary Blue**: `#3b82f6` (Blue-500)
- **Primary Blue Dark**: `#2563eb` (Blue-600)
- **Primary Blue Darker**: `#1d4ed8` (Blue-700)
- **Primary Blue Darkest**: `#1e40af` (Blue-800)

### Secondary Colors
- **Success Green**: `#10b981` (Emerald-500)
- **Success Green Dark**: `#059669` (Emerald-600)
- **Success Green Darker**: `#047857` (Emerald-700)

- **Warning Orange**: `#f97316` (Orange-500)
- **Warning Orange Dark**: `#ea580c` (Orange-600)

- **Danger Red**: `#ef4444` (Red-500)
- **Danger Red Dark**: `#dc2626` (Red-600)
- **Danger Red Darker**: `#b91c1c` (Red-700)

- **Info Purple**: `#8b5cf6` (Purple-500)
- **Info Purple Dark**: `#7c3aed` (Purple-600)

- **Info Cyan**: `#06b6d4` (Cyan-500)
- **Info Cyan Dark**: `#0891b2` (Cyan-600)

### Neutral Colors
- **Gray 50**: `#f9fafb` (Light backgrounds)
- **Gray 100**: `#f3f4f6` (Subtle backgrounds)
- **Gray 200**: `#e5e7eb` (Borders)
- **Gray 300**: `#d1d5db` (Light borders)
- **Gray 400**: `#9ca3af` (Disabled text)
- **Gray 500**: `#6b7280` (Secondary text)
- **Gray 600**: `#4b5563` (Muted text)
- **Gray 700**: `#374151` (Primary text)
- **Gray 800**: `#1f2937` (Dark text)
- **Gray 900**: `#111827` (Darkest text)

## 🎨 Gradient Patterns

### Primary Gradients
```css
/* Blue Primary */
background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
hover: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);

/* Green Success */
background: linear-gradient(135deg, #10b981 0%, #059669 100%);
hover: linear-gradient(135deg, #059669 0%, #047857 100%);

/* Red Danger */
background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
hover: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);

/* Orange Warning */
background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
hover: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);

/* Purple Info */
background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
hover: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);

/* Cyan Info */
background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
hover: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
```

### Navigation Gradient
```css
/* Navigation Bar */
background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
border-bottom: 2px solid #1e40af;
```

### Table Header Gradient
```css
/* Table Headers */
background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
border-bottom: 2px solid #3b82f6;
```

## 🎯 Component Color Usage

### Buttons
- **Primary Actions**: Blue gradient
- **Success Actions**: Green gradient (Import, Create)
- **Danger Actions**: Red gradient (Delete, Export PDF)
- **Info Actions**: Purple gradient (View, Details)
- **Warning Actions**: Orange gradient (Export, Alerts)

### Status Badges
- **Created**: Green gradient with dark green text
- **Updated**: Blue gradient with dark blue text
- **Deleted**: Red gradient with dark red text
- **Login**: Emerald gradient with dark emerald text
- **Logout**: Gray gradient with dark gray text
- **Import**: Purple gradient with dark purple text
- **Export**: Orange gradient with dark orange text
- **Bulk Operation**: Indigo gradient with dark indigo text

### Statistics Cards
- **Total Activities**: Blue gradient
- **Logins**: Green gradient
- **Imports**: Purple gradient
- **Exports**: Orange gradient
- **Creates**: Emerald gradient
- **Updates**: Cyan gradient

### Tables
- **Header**: Light gray gradient with blue border
- **Rows**: White background with gray borders
- **Hover**: Light gray background
- **Alternating**: Subtle gray background

### Forms
- **Input Focus**: Blue ring and border
- **Labels**: Gray-700 text
- **Placeholders**: Gray-500 text
- **Error States**: Red border and text

## 🎨 Implementation Guidelines

### Inline Styles (Recommended)
Use inline styles for gradients to ensure they work regardless of CSS compilation:

```html
<div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
    <!-- Content -->
</div>
```

### Hover Effects
Always include hover effects for interactive elements:

```html
<button style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
        onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
        onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
    Button Text
</button>
```

### Text Colors
- **White text** on colored/gradient backgrounds
- **Gray-900** for primary text on white backgrounds
- **Gray-600** for secondary text
- **Gray-500** for muted text

### Shadows and Borders
- **Cards**: `shadow-lg` with `border border-gray-100`
- **Buttons**: `shadow-lg hover:shadow-xl`
- **Tables**: `border border-gray-200`

## 🎯 Accessibility

### Contrast Ratios
- All text meets WCAG AA standards
- White text on colored backgrounds
- Dark text on light backgrounds
- Sufficient contrast for all interactive elements

### Color Blindness
- Use both color and icons for status indicators
- Ensure information is not conveyed by color alone
- Test with color blindness simulators

## 🎨 Future Updates

When adding new components:
1. Follow the established color patterns
2. Use gradients for primary actions
3. Maintain consistent hover effects
4. Ensure accessibility compliance
5. Update this guide with new patterns

## 📝 Notes

- All gradients use 135-degree angles for consistency
- Hover effects darken the gradient by one shade
- Use `transition-all duration-150` for smooth animations
- Prefer inline styles for gradients to avoid CSS compilation issues
- Test colors in both light and dark modes when applicable
