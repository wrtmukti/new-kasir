---
name: Urban Skena
colors:
  surface: '#fcf8f9'
  surface-dim: '#dcd9da'
  surface-bright: '#fcf8f9'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f6f3f4'
  surface-container: '#f0edee'
  surface-container-high: '#eae7e8'
  surface-container-highest: '#e5e2e3'
  on-surface: '#1b1b1c'
  on-surface-variant: '#3d4a3d'
  inverse-surface: '#303031'
  inverse-on-surface: '#f3f0f1'
  outline: '#6d7b6c'
  outline-variant: '#bccbb9'
  surface-tint: '#006e2f'
  primary: '#006e2f'
  on-primary: '#ffffff'
  primary-container: '#22c55e'
  on-primary-container: '#004b1e'
  inverse-primary: '#4ae176'
  secondary: '#0058be'
  on-secondary: '#ffffff'
  secondary-container: '#2170e4'
  on-secondary-container: '#fefcff'
  tertiary: '#9d4300'
  on-tertiary: '#ffffff'
  tertiary-container: '#ff8e4d'
  on-tertiary-container: '#6d2d00'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#6bff8f'
  primary-fixed-dim: '#4ae176'
  on-primary-fixed: '#002109'
  on-primary-fixed-variant: '#005321'
  secondary-fixed: '#d8e2ff'
  secondary-fixed-dim: '#adc6ff'
  on-secondary-fixed: '#001a42'
  on-secondary-fixed-variant: '#004395'
  tertiary-fixed: '#ffdbca'
  tertiary-fixed-dim: '#ffb690'
  on-tertiary-fixed: '#341100'
  on-tertiary-fixed-variant: '#783200'
  background: '#fcf8f9'
  on-background: '#1b1b1c'
  surface-variant: '#e5e2e3'
typography:
  display-xl:
    fontFamily: Syne
    fontSize: 64px
    fontWeight: '800'
    lineHeight: '1.1'
    letterSpacing: -0.04em
  headline-lg:
    fontFamily: Syne
    fontSize: 40px
    fontWeight: '800'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Syne
    fontSize: 32px
    fontWeight: '800'
    lineHeight: '1.2'
  headline-md:
    fontFamily: Syne
    fontSize: 24px
    fontWeight: '700'
    lineHeight: '1.3'
  body-lg:
    fontFamily: Space Grotesk
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Space Grotesk
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  label-bold:
    fontFamily: Space Grotesk
    fontSize: 14px
    fontWeight: '600'
    lineHeight: '1.2'
    letterSpacing: 0.05em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 4px
  xs: 8px
  sm: 16px
  md: 24px
  lg: 40px
  xl: 64px
  gutter: 20px
  margin-mobile: 16px
  margin-desktop: 48px
---

## Brand & Style
The design system draws inspiration from the Indonesian "Skena" subculture—a vibrant intersection of indie music, third-wave coffee culture, and urban fashion. The personality is high-energy, expressive, and unapologetically bold. It targets a creative, youth-driven audience that values authenticity and "indie" aesthetics over corporate polish.

The style is a fusion of **Neo-Brutalism** and **Modern-Vintage**. It utilizes heavy outlines, high-contrast intersections, and intentional "ink-trap" typography to create a tactile, printed feel. The interface should feel like a digital fanzine: structured but rhythmic, clean but loud.

## Colors
The palette is anchored by a bright, textured off-white background to prevent digital fatigue while maintaining a high-energy vibe.

- **Skena Green (#22C55E):** The primary driver. Used for core actions, success states, and primary brand markers.
- **Electric Blue (#3B82F6):** The secondary energy. Used for interactive elements, links, and secondary badges.
- **Pop Orange (#F97316):** An accent color used sparingly for notifications, highlights, or "New" tags.
- **Ink Black (#1A1A1B):** Used for all borders, text, and heavy shadows to provide the brutalist structure.
- **Cream (#FDFCF0):** Used for secondary surface areas like card backgrounds to provide a vintage paper feel.

## Typography
The typographic pairing emphasizes the "Skena" aesthetic by mixing expressive display faces with technical body text.

- **Headlines:** Use **Syne** for all headlines. It should be set with tight letter-spacing and heavy weights. For display sizes, use the "Extra Bold" or "Stencil" variants if available to lean into the artistic vibe.
- **Body & UI:** Use **Space Grotesk** for all functional text. Its monospaced-adjacent proportions provide a "techy" and clean contrast to the eccentric headlines.
- **Hierarchy:** Ensure a dramatic scale difference between headlines and body text. Labels should almost always be uppercase with slight tracking to mimic industrial labeling.

## Layout & Spacing
The layout follows a **Rigid Grid** philosophy. Content should feel boxed and contained, utilizing thick borders to define sections rather than white space alone.

- **Grid:** Use a 12-column grid for desktop and a 4-column grid for mobile. 
- **Borders:** All primary containers must have a 2px or 3px solid Ink Black border.
- **Rhythm:** Use a strict 8px-based spacing system, but allow for "intentional overlap" where elements might break the grid slightly (e.g., a sticker-style badge overlapping a card border).
- **Adaptation:** On mobile, margins should be tight (16px) to maximize the "packed" feel of an indie flyer.

## Elevation & Depth
This design system rejects soft shadows and ambient blurs. Instead, it uses **Hard-Edge Shadows** and **Tonal Layering**.

- **Hard Shadows:** Use "Offset Shadows" for buttons and cards. This is achieved by a solid block of Ink Black offset by 4px or 8px (bottom-right), with no blur.
- **Tonal Layers:** Deep hierarchy is created by nesting containers with different background colors (e.g., a Cream card inside a Green section).
- **Outlines:** Every interactive element must have a visible 2px border. Depth is signaled by the thickness of the offset shadow, not the blur radius.

## Shapes
The shape language is primarily **Geometric and Sharp**. 

- **Corners:** Use a consistent 4px (Soft) radius for most UI components (buttons, inputs, cards). This provides a hint of modernity without losing the brutalist edge.
- **Interactive States:** When a button is "pressed," it should lose its offset shadow and translate 4px down/right, mimicking a physical click into the page.
- **Icons:** Use thick-stroke icons (2px minimum) with sharp caps to match the border weights of the UI elements.

## Components
- **Buttons:** Primary buttons use Skena Green with a 2px Ink Black border and an 4px black offset shadow. Text is uppercase Label-Bold.
- **Inputs:** Use white backgrounds with 2px borders. On focus, the border remains black but the background shifts to Accent Cream.
- **Cards:** Cards should use the Cream background (#FDFCF0) to stand out against the Off-White page. They always feature a heavy 8px offset shadow.
- **Chips/Badges:** Use Electric Blue or Pop Orange. These should have 0px roundedness (sharp corners) to distinguish them from buttons.
- **Lists:** Items are separated by solid 2px horizontal lines. Hover states involve a full-color background fill (Skena Green) for the entire row.
- **Specialty Component - "The Sticker":** A floating badge or call-to-action that is rotated by 2-3 degrees, mimicking a hand-placed sticker on a cafe wall.