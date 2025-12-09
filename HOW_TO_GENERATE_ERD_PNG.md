# How to Generate PNG ERD from Mermaid Diagram

Since the image generation API has reached its quota, here are **3 easy ways** to convert the Mermaid ERD to PNG:

---

## Method 1: Online Mermaid Editor (Easiest - 2 minutes)

1. **Go to:** https://mermaid.live/
2. **Copy** the Mermaid code from `DATABASE_ERD.md` (lines 10-247)
3. **Paste** into the left editor panel
4. **Click** the "PNG" or "SVG" download button in the top-right
5. **Save** the image to your project folder

✅ **Pros:** No installation, instant preview, high quality
❌ **Cons:** Requires internet connection

---

## Method 2: VS Code Extension (Best for developers)

1. **Install Extension:**
   - Open VS Code
   - Go to Extensions (Ctrl+Shift+X / Cmd+Shift+X)
   - Search for "Markdown Preview Mermaid Support"
   - Install it

2. **Generate PNG:**
   - Open `DATABASE_ERD.md` in VS Code
   - Press `Ctrl+Shift+V` (Cmd+Shift+V on Mac) to preview
   - Right-click on the diagram → "Copy Image" or "Save Image As"

✅ **Pros:** Works offline, integrated with your editor
❌ **Cons:** Requires VS Code extension

---

## Method 3: Mermaid CLI (For automation)

1. **Install Mermaid CLI:**
   ```bash
   npm install -g @mermaid-js/mermaid-cli
   ```

2. **Create a file** `erd.mmd` with just the Mermaid code (from DATABASE_ERD.md)

3. **Generate PNG:**
   ```bash
   mmdc -i erd.mmd -o hrms_database_erd.png -b white -w 3000 -H 4000
   ```

   Options explained:
   - `-i` = input file
   - `-o` = output file
   - `-b white` = white background
   - `-w 3000` = width 3000px
   - `-H 4000` = height 4000px

✅ **Pros:** Scriptable, high resolution, customizable
❌ **Cons:** Requires Node.js and CLI tools

---

## Method 4: GitHub (If you use GitHub)

1. **Create a new file** in your GitHub repo: `docs/ERD.md`
2. **Paste** the Mermaid code from `DATABASE_ERD.md`
3. **Commit** the file
4. **View** on GitHub - it will render automatically
5. **Take a screenshot** or use browser extensions to save as PNG

✅ **Pros:** Version controlled, shareable link
❌ **Cons:** Requires GitHub, screenshot quality varies

---

## Method 5: Draw.io / Diagrams.net (Manual but customizable)

1. **Go to:** https://app.diagrams.net/
2. **Use the Mermaid import:**
   - Click "Arrange" → "Insert" → "Advanced" → "Mermaid"
   - Paste the Mermaid code
3. **Customize** colors, layout, fonts as needed
4. **Export** as PNG: File → Export as → PNG

✅ **Pros:** Highly customizable, professional output
❌ **Cons:** Manual work, takes longer

---

## Recommended Approach

**For Quick Results:** Use **Method 1** (mermaid.live) - takes 2 minutes

**For Best Quality:** Use **Method 3** (Mermaid CLI) with these settings:
```bash
mmdc -i erd.mmd -o hrms_database_erd.png -b white -w 4000 -H 5000 -s 2
```

This will create a high-resolution PNG perfect for documentation and presentations.

---

## Alternative: I've Created an ASCII Visual

I've also created `DATABASE_ERD_VISUAL.txt` which shows the database structure in ASCII art format. While not as pretty as a PNG, it's:
- ✅ Immediately viewable in any text editor
- ✅ Version control friendly
- ✅ Works in terminals and documentation
- ✅ Shows all relationships clearly

---

## Files Created

1. **DATABASE_ERD.md** - Full Mermaid diagram with documentation
2. **DATABASE_ERD_VISUAL.txt** - ASCII art visual representation
3. **This file** - Instructions to generate PNG

---

## Need Help?

If you want me to help with any of these methods, just let me know which one you'd like to try!
