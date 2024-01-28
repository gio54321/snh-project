-- mock data for testing

USE yasbs;

INSERT INTO `books` (`title`, `author`, `description`, `price`, `image`) VALUES
(
    "The shellcoder's handbook",
    "Chris Anley, Gerardo Richarte, Felix Lindner, John Heasman",
    "The black hats have kept up with security enhancements. Have you?
In the technological arena, three years is a lifetime. Since the first edition of this book was published in 2004, built-in security measures on compilers and operating systems have become commonplace, but are still far from perfect. Arbitrary-code execution vulnerabilities still allow attackers to run code of their choice on your system - with disastrous results.",
    4597,
    "/static/shellcoder_handbook.jpg"
),
(
    "Deep work",
    "Cal Newport",
    "Deep work is the ability to focus without distraction on a cognitively demanding task. It's a skill that allows you to quickly master complicated information and produce better results in less time. Deep Work will make you better at what you do and provide the sense of true fulfillment that comes from craftsmanship. In short, deep work is like a super power in our increasingly competitive twenty-first century economy. And yet, most people have lost the ability to go deep-spending their days instead in a frantic blur of e-mail and social media, not even realizing there's a better way.

In Deep Work, author and professor Cal Newport flips the narrative on impact in a connected age. Instead of arguing distraction is bad, he instead celebrates the power of its opposite. Dividing this book into two parts, he first makes the case that in almost any profession, cultivating a deep work ethic will produce massive benefits. He then presents a rigorous training regimen, presented as a series of four rules, for transforming your mind and habits to support this skill.",
    1968,
    "/static/deep_work.jpg"
),
(
    "The Great Gatsby",
    "F. Scott Fitzgerald",
    "The Great Gatsby is a 1925 novel written by American author F. Scott Fitzgerald that follows a cast of characters living in the fictional towns of West Egg and East Egg on prosperous Long Island in the summer of 1922. The story primarily concerns the young and mysterious millionaire Jay Gatsby and his quixotic passion and obsession with the beautiful former debutante Daisy Buchanan. Considered to be Fitzgerald's magnum opus, The Great Gatsby explores themes of decadence, idealism, resistance to change, social upheaval, and excess, creating a portrait of the Jazz Age or the Roaring Twenties that has been described as a cautionary tale regarding the American Dream.",
    999,
    "/static/the_great_gatsby.jpg"
),
(
    "Practical foundations for programming languages",
    "Robert Harper",
    "My purpose in writing this book is to establish a comprehensive framework for formulating and analyzing a broad range of ideas in programming languages. If language design and programming methodology are to advance from a trade-craft to a rigorous discipline, it is essential that we first get the definitions right. Then, and only then, can there be meaningful analysis and consolidation of ideas. My hope is that I have helped to build such a foundation.",
    5467,
    "/static/pfpl.jpg"
),
(
    "TCP/IP illustrated - Volume 1",
    "William Richard (Rich) Stevens",
    "TCP/IP Illustrated, Volume 1, Second Edition,is a detailed and visual guide to today's TCP/IP protocol suite. Fully updated for the newest innovations, it demonstrates each protocol in action through realistic examples from modern Linux, Windows, and Mac OS environments. There's no better way to discover why TCP/IP works as it does, how it reacts to common conditions, and how to apply it in your own applications and networks.",
    4149,
    "/static/tcp_ip_1.jpg"
);