---
name: Jogja Artisan Cafe
colors:
  surface: '#fdf9f4'
  surface-dim: '#ddd9d5'
  surface-bright: '#fdf9f4'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f7f3ee'
  surface-container: '#f1ede8'
  surface-container-high: '#ebe8e3'
  surface-container-highest: '#e6e2dd'
  on-surface: '#1c1c19'
  on-surface-variant: '#56423e'
  inverse-surface: '#31302d'
  inverse-on-surface: '#f4f0eb'
  outline: '#89726d'
  outline-variant: '#dcc1ba'
  surface-tint: '#9c432d'
  primary: '#943d28'
  on-primary: '#ffffff'
  primary-container: '#b3543d'
  on-primary-container: '#fff3f0'
  inverse-primary: '#ffb4a3'
  secondary: '#725a42'
  on-secondary: '#ffffff'
  secondary-container: '#fedcbe'
  on-secondary-container: '#796048'
  tertiary: '#3c4fb2'
  on-tertiary: '#ffffff'
  tertiary-container: '#5668cd'
  on-tertiary-container: '#f6f4ff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdad2'
  primary-fixed-dim: '#ffb4a3'
  on-primary-fixed: '#3d0700'
  on-primary-fixed-variant: '#7d2c18'
  secondary-fixed: '#fedcbe'
  secondary-fixed-dim: '#e1c1a4'
  on-secondary-fixed: '#291806'
  on-secondary-fixed-variant: '#59422c'
  tertiary-fixed: '#dee0ff'
  tertiary-fixed-dim: '#bac3ff'
  on-tertiary-fixed: '#00105c'
  on-tertiary-fixed-variant: '#293ca0'
  background: '#fdf9f4'
  on-background: '#1c1c19'
  surface-variant: '#e6e2dd'
typography:
  display-lg:
    fontFamily: Playfair Display
    fontSize: 48px
    fontWeight: '700'
    lineHeight: '1.1'
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 36px
    fontWeight: '700'
    lineHeight: '1.2'
  headline-md:
    fontFamily: Playfair Display
    fontSize: 32px
    fontWeight: '600'
    lineHeight: '1.3'
  headline-sm:
    fontFamily: Playfair Display
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.4'
  body-lg:
    fontFamily: Manrope
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Manrope
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  label-md:
    fontFamily: Manrope
    fontSize: 14px
    fontWeight: '600'
    lineHeight: '1.2'
    letterSpacing: 0.05em
  caption:
    fontFamily: Manrope
    fontSize: 12px
    fontWeight: '500'
    lineHeight: '1.4'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 8px
  container-max: 1200px
  gutter: 24px
  margin-mobile: 20px
  margin-desktop: 64px
  section-gap: 80px
---

## Brand & Style

This design system captures the "nongkrong" (hanging out) culture of Yogyakarta, blending traditional Javanese warmth with modern cafe minimalism. The aesthetic is **Artisanal Minimalism**—a style that prioritizes tactile warmth and organic balance over clinical precision.

The target audience consists of coffee enthusiasts, digital nomads, and culture seekers who value authenticity and a slower pace of life. The UI should feel like stepping into a renovated heritage building: grounded, airy, and rich with subtle textures. 

Key visual principles include:
- **Rustic Tactility:** Use of soft paper-like backgrounds and faint, geometric batik patterns (Parang or Kawung) as low-opacity overlays to add depth.
- **Organic Composition:** Generous whitespace that feels intentional rather than empty, mimicking the open-air layout of a traditional Joglo house.
- **Handcrafted Precision:** UI elements should feel carefully placed and "heavy" in a satisfying, grounded way.

## Colors

The palette is derived from natural Javanese materials and heritage dyes.

- **Primary (Terracotta):** Used for primary actions and highlights, reminiscent of traditional roof tiles and clay pottery.
- **Secondary (Teak Wood):** Used for deep text, headers, and structural elements to provide a sense of stability.
- **Tertiary (Batik Indigo):** An accent color reserved for special callouts, links, or subtle decorative motifs, referencing traditional textile arts.
- **Neutral (Cream/Paper):** The primary background color. It is a warm, desaturated off-white that reduces eye strain and enhances the rustic feel.

**Application Notes:**
- Backgrounds should rarely be pure white; use the Cream neutral to maintain warmth.
- Use the Teak Wood for high-contrast borders and the Terracotta for moments of high energy.

## Typography

The typographic system creates a tension between the expressive, literary qualities of **Playfair Display** and the clean, functional modernity of **Manrope**.

- **Headlines:** Always use Playfair Display. For large display sizes, a slight negative letter-spacing is recommended to create a sophisticated, editorial look.
- **Body Text:** Manrope provides excellent legibility for menus, descriptions, and long-form content. 
- **Labels:** Use Manrope in SemiBold or Bold with slightly increased letter-spacing and uppercase styling for small navigation elements or category tags.

**Hierarchy Tip:** Pair a large Serif headline with a small, tracked-out Sans-Serif label above it to evoke a boutique cafe menu aesthetic.

## Layout & Spacing

This design system utilizes a **Fluid Grid** with wide margins to create a relaxed, unhurried atmosphere.

- **Grid:** A 12-column grid for desktop, 6-column for tablet, and 2-column for mobile.
- **Rhythm:** An 8px base unit drives all padding and margin decisions. 
- **Sectioning:** Use large vertical gaps (`section-gap`) between content blocks to ensure the UI feels "airy" and "breathable," echoing the spaciousness of Javanese architecture.
- **Alignment:** While the grid is structured, allow for occasional asymmetrical placement of images or decorative Batik motifs to break the digital rigidity.

## Elevation & Depth

To maintain a rustic and artisanal feel, avoid heavy, blurry shadows that look overly "digital." Instead, use:

- **Tonal Layering:** Create depth by placing slightly darker cream or very light terracotta surfaces on top of the main background.
- **Soft Insets:** For input fields or containers, use a very subtle inner shadow or a 1px border in a slightly darker shade of the background color to suggest a "pressed" or "carved" effect.
- **Ambient Lift:** When an element must float (like a modal or a floating action button), use a very soft, multi-layered shadow with a Teak Wood (#4B3621) tint at 5-10% opacity.
- **Outlines:** Use thin (1px) borders in Teak Wood for a grounded, paper-like feel.

## Shapes

The shape language is **Soft and Organic**. Avoid sharp 90-degree angles which feel too aggressive for the "nongkrong" vibe.

- **Standard Elements:** Buttons and cards use a `0.5rem` (8px) radius.
- **Feature Elements:** Use `1.5rem` or fully rounded pill shapes for tags and chips.
- **Images:** Apply the `rounded-xl` (1.5rem) setting to photos of food or interiors to make them feel more integrated into the soft aesthetic.
- **Motifs:** Incorporate circular or arched shapes in background decorations to mimic Javanese architectural archways.

## Components

### Buttons
- **Primary:** Solid Terracotta background with Cream text. Medium weight.
- **Secondary:** Teak Wood outline (1px) with Teak Wood text.
- **Tertiary:** No background or border; Teak Wood text with a small Terracotta underline or dot motif.

### Cards
- Use the Cream background with a very thin Teak Wood border (0.5px) or a subtle tonal shift. 
- Cards should have generous internal padding (min 24px).

### Input Fields
- Softly rounded corners (`rounded-lg`).
- Background should be a slightly lighter or darker tint of the page background.
- Focus state uses a 2px Terracotta bottom border rather than a full glow.

### Chips & Tags
- Pill-shaped (`rounded-xl`).
- Use Batik Indigo backgrounds with white text for "New" or "Special" items.
- Use Teak Wood backgrounds for standard categories.

### Lists
- Use thin, horizontal Teak Wood dividers with 10% opacity.
- Icons should be minimal, line-art style, using the Teak Wood color.

### Special Component: "The Batik Divider"
- A decorative horizontal rule that incorporates a tiny, repeating geometric batik motif in the center, used to separate major sections of a page.