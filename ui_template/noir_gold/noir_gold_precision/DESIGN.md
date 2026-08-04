---
name: Noir & Gold Precision
colors:
  surface: '#131313'
  surface-dim: '#131313'
  surface-bright: '#3a3939'
  surface-container-lowest: '#0e0e0e'
  surface-container-low: '#1c1b1b'
  surface-container: '#201f1f'
  surface-container-high: '#2a2a2a'
  surface-container-highest: '#353534'
  on-surface: '#e5e2e1'
  on-surface-variant: '#d0c5af'
  inverse-surface: '#e5e2e1'
  inverse-on-surface: '#313030'
  outline: '#99907c'
  outline-variant: '#4d4635'
  surface-tint: '#e9c349'
  primary: '#f2ca50'
  on-primary: '#3c2f00'
  primary-container: '#d4af37'
  on-primary-container: '#554300'
  inverse-primary: '#735c00'
  secondary: '#c8c6c5'
  on-secondary: '#313030'
  secondary-container: '#474746'
  on-secondary-container: '#b7b5b4'
  tertiary: '#d1cec7'
  on-tertiary: '#31312b'
  tertiary-container: '#b5b3ac'
  on-tertiary-container: '#464540'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#ffe088'
  primary-fixed-dim: '#e9c349'
  on-primary-fixed: '#241a00'
  on-primary-fixed-variant: '#574500'
  secondary-fixed: '#e5e2e1'
  secondary-fixed-dim: '#c8c6c5'
  on-secondary-fixed: '#1c1b1b'
  on-secondary-fixed-variant: '#474746'
  tertiary-fixed: '#e5e2db'
  tertiary-fixed-dim: '#c9c6bf'
  on-tertiary-fixed: '#1c1c17'
  on-tertiary-fixed-variant: '#474741'
  background: '#131313'
  on-background: '#e5e2e1'
  surface-variant: '#353534'
typography:
  display-lg:
    fontFamily: Bodoni Moda
    fontSize: 64px
    fontWeight: '700'
    lineHeight: '1.1'
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Bodoni Moda
    fontSize: 40px
    fontWeight: '700'
    lineHeight: '1.2'
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Bodoni Moda
    fontSize: 32px
    fontWeight: '600'
    lineHeight: '1.3'
  headline-sm:
    fontFamily: Bodoni Moda
    fontSize: 24px
    fontWeight: '500'
    lineHeight: '1.4'
  body-lg:
    fontFamily: Hanken Grotesk
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
    letterSpacing: 0.01em
  body-md:
    fontFamily: Hanken Grotesk
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  label-caps:
    fontFamily: Hanken Grotesk
    fontSize: 12px
    fontWeight: '600'
    lineHeight: '1'
    letterSpacing: 0.2em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  unit: 8px
  container-max: 1200px
  gutter: 32px
  margin-desktop: 80px
  margin-mobile: 24px
---

## Brand & Style

The design system is rooted in the concept of "Quiet Luxury"—an aesthetic that prioritizes material quality and precision over loud ornamentation. The target audience is an ultra-high-net-worth demographic that values exclusivity, craftsmanship, and a frictionless experience.

The style is a synthesis of **Minimalism** and **High-Contrast Luxury**. It utilizes vast expanses of "Obsidian" whitespace to create a sense of architectural scale, punctuated by razor-thin "Champagne Gold" accents that suggest the precision of a Swiss timepiece. The interface should feel like a high-end concierge: silent, efficient, and impeccably presented. 

Key visual principles:
- **Spatial Dignity:** Generous padding and margins to ensure no element feels crowded.
- **Micro-Precision:** Use of 0.5pt to 1pt strokes for dividers and borders.
- **Materiality:** UI elements should mimic physical premium materials—matte metals, brushed surfaces, and silk-screened textures.

## Colors

The palette is anchored in a dark, atmospheric foundation to evoke the "Noir" namesake.

- **Primary (Champagne Gold):** Used sparingly for interactive elements, call-to-actions, and subtle brand accents. It should never be used for large backgrounds.
- **Secondary (Matte Charcoal):** Used for container surfaces and elevated UI elements to create soft contrast against the deep black.
- **Tertiary (Ivory Silk):** Reserved primarily for high-contrast typography and occasional light-mode surfaces to provide relief from the darkness.
- **Neutral (Obsidian Black):** The primary background color. It is a deep, true black that allows the gold and ivory elements to "pop" with photographic clarity.

## Typography

Typography is the primary vehicle for the brand’s "Precision" narrative. 

**Bodoni Moda** serves as the display typeface. Its high vertical contrast and sharp serifs evoke editorial prestige. It should be used for product names, price points, and section headers.

**Hanken Grotesk** provides a clean, modern counterpoint. For labels and navigation, use the `label-caps` style with wide tracking (20%) to emulate the engraving on luxury watches. All body text should maintain a generous line-height to ensure the reading experience is effortless and airy.

## Layout & Spacing

The layout follows a **Fixed Grid** philosophy on desktop to maintain a curated, gallery-like feel, while transitioning to a fluid model on mobile.

- **Desktop:** 12-column grid with wide 80px outer margins. This creates a "stage" for the content, emphasizing the premium nature of the products.
- **Rhythm:** Use an 8px base unit. Vertical spacing between sections should be aggressive (e.g., 120px or 160px) to signify "Quiet Luxury" through the intentional "waste" of screen real estate.
- **Dividers:** Horizontal and vertical dividers should be 0.5px or 1px thick, colored in low-opacity Champagne Gold or Matte Charcoal.

## Elevation & Depth

This design system avoids heavy drop shadows in favor of **Tonal Layers** and **Subtle Outlines**.

- **Surfaces:** Depth is created by placing Matte Charcoal (`#1A1A1A`) containers on top of the Obsidian Black (`#0A0A0A`) background. 
- **Outlines:** Instead of shadows, use "Ghost Borders"—1px solid borders with 10-15% opacity. For active or hovered states, increase the border opacity or transition the color to Champagne Gold.
- **Glassmorphism:** Use sparingly for navigation bars. A 20px backdrop blur with a 10% white tint provides a "frosted obsidian" effect that maintains legibility over scrolling content.

## Shapes

The shape language is architectural and sharp. 

- **Primary Corners:** A subtle 4px (`0.25rem`) radius is used for buttons and cards to soften the "Brutalist" edge just enough to feel sophisticated rather than aggressive.
- **Interactive Elements:** Buttons should be strictly rectangular or use the 4px radius. Avoid pill shapes or circles, as they lean too casual for this brand narrative.
- **Imagery:** Product photography should always be framed in sharp-edged containers or with the same subtle 4px radius, never cropped into circles.

## Components

### Buttons
- **Primary:** Ivory Silk background with Obsidian Black text. No border. On hover, background shifts to Champagne Gold.
- **Secondary:** Transparent background with a 1px Champagne Gold border. Label in `label-caps` style.
- **Tertiary/Ghost:** No background or border. Champagne Gold text with a thin underline that expands on hover.

### Input Fields
- Underline-only style. A 1px Charcoal line that turns into a 1px Gold line on focus. Labels appear in `label-caps` above the field.

### Cards (Product/Menu)
- No background or a very subtle Matte Charcoal fill. Focus is on high-resolution photography. Pricing is displayed in Bodoni Moda, positioned with intentional asymmetry.

### Lists
- Menu items should be separated by full-width 0.5px gold dividers. Each item should have significant vertical padding (24px+) to prevent a "cluttered menu" feel.

### Additional Components
- **The "Gold Thread":** A decorative 1px vertical line used to guide the eye between sections or connect a headline to its body text, reinforcing the "Precision" aspect of the brand.