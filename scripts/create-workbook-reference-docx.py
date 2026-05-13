#!/usr/bin/env python3
"""Create a Pandoc reference DOCX with Noto Sans as the default font."""

from __future__ import annotations

import sys
from pathlib import Path
from docx import Document
from docx.shared import Pt


def apply_style(document: Document, style_name: str, size: int) -> None:
    if style_name not in document.styles:
        return
    style = document.styles[style_name]
    style.font.name = "Noto Sans"
    style.font.size = Pt(size)


def main() -> int:
    default_output = Path(__file__).resolve().parent.parent / "styles" / "workbook-reference.docx"
    output_path = Path(sys.argv[1]).resolve() if len(sys.argv) > 1 else default_output
    output_path.parent.mkdir(parents=True, exist_ok=True)
    document = Document()

    apply_style(document, "Normal", 11)
    apply_style(document, "Heading 1", 20)
    apply_style(document, "Heading 2", 16)
    apply_style(document, "Heading 3", 13)

    document.save(str(output_path))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
