-- mock data for testing

USE yasbs;

INSERT INTO `books` (`title`, `author`, `description`, `price`, `image`, `file`) VALUES
(
    "The shellcoder's handbook",
    "Chris Anley, Gerardo Richarte, Felix Lindner, John Heasman",
    "The black hats have kept up with security enhancements. Have you?
In the technological arena, three years is a lifetime. Since the first edition of this book was published in 2004, built-in security measures on compilers and operating systems have become commonplace, but are still far from perfect. Arbitrary-code execution vulnerabilities still allow attackers to run code of their choice on your system - with disastrous results.",
    4597,
    "/static/shellcoder_handbook.jpg",
    "./books/example.pdf"
),
(
    "The Moonmath Manual",
    "LeastAuthority",
    "The MoonMath Manual is a resource for anyone interested in understanding and unlocking the potential of zk-SNARKs, from beginners to experts.
The acronym zk-SNARK stands for Zero-Knowledge Succinct Non-Interactive Argument of Knowledge, and refers to a cryptographic technique where one can prove correct execution of computation and possession of certain information without revealing the information itself. Zk-SNARKs, a type of zero-knowledge proof, offer a new paradigm for privacy, and have been used to enable private blockchain transactions. They also have the potential to securely scale blockchain-based solutions.
As users go through the manual, they will grasp mathematical concepts that are not only used in SNARKs, but also in other zero-knowledge proofs and cryptography more generally.",
    3298,
    "/static/moonmath.webp",
    "./books/example.pdf"
),
(
    "Deep work",
    "Cal Newport",
    "Deep work is the ability to focus without distraction on a cognitively demanding task. It's a skill that allows you to quickly master complicated information and produce better results in less time. Deep Work will make you better at what you do and provide the sense of true fulfillment that comes from craftsmanship. In short, deep work is like a super power in our increasingly competitive twenty-first century economy. And yet, most people have lost the ability to go deep-spending their days instead in a frantic blur of e-mail and social media, not even realizing there's a better way.

In Deep Work, author and professor Cal Newport flips the narrative on impact in a connected age. Instead of arguing distraction is bad, he instead celebrates the power of its opposite. Dividing this book into two parts, he first makes the case that in almost any profession, cultivating a deep work ethic will produce massive benefits. He then presents a rigorous training regimen, presented as a series of four rules, for transforming your mind and habits to support this skill.",
    1968,
    "/static/deep_work.jpg",
    "./books/example.pdf"
),
(
    "Practical foundations for programming languages",
    "Robert Harper",
    "My purpose in writing this book is to establish a comprehensive framework for formulating and analyzing a broad range of ideas in programming languages. If language design and programming methodology are to advance from a trade-craft to a rigorous discipline, it is essential that we first get the definitions right. Then, and only then, can there be meaningful analysis and consolidation of ideas. My hope is that I have helped to build such a foundation.",
    5467,
    "/static/pfpl.jpg",
    "./books/example.pdf"
),
(
    "TCP/IP illustrated - Volume 1",
    "William Richard (Rich) Stevens",
    "TCP/IP Illustrated, Volume 1, Second Edition,is a detailed and visual guide to today's TCP/IP protocol suite. Fully updated for the newest innovations, it demonstrates each protocol in action through realistic examples from modern Linux, Windows, and Mac OS environments. There's no better way to discover why TCP/IP works as it does, how it reacts to common conditions, and how to apply it in your own applications and networks.",
    4149,
    "/static/tcp_ip_1.jpg",
    "./books/example.pdf"
),
(
    "An introduction to mathematical cryptography",
    "Jeffrey Hoffstein",
    "This self-contained introduction to modern cryptography emphasizes the mathematics behind the theory of public key cryptosystems and digital signature schemes. The book focuses on these key topics while developing the mathematical tools needed for the construction and security analysis of diverse cryptosystems. Only basic linear algebra is required of the reader; techniques from algebra, number theory, and probability are introduced and developed as required. This text provides an ideal introduction for mathematics and computer science students to the mathematical foundations of modern cryptography. The book includes an extensive bibliography and index; supplementary materials are available online.",
    5884,
    "/static/crypto.jpg",
    "./books/example.pdf"
);