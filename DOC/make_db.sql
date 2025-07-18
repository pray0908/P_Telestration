USE [master]
GO
/****** Object:  Database [TelestrationGame]    Script Date: 2025-07-18 오후 5:48:12 ******/
CREATE DATABASE [TelestrationGame]
 CONTAINMENT = NONE
 ON  PRIMARY 
( NAME = N'TelestrationGame', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL11.SQLEXPRESS\MSSQL\DATA\TelestrationGame.mdf' , SIZE = 4160KB , MAXSIZE = UNLIMITED, FILEGROWTH = 1024KB )
 LOG ON 
( NAME = N'TelestrationGame_log', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL11.SQLEXPRESS\MSSQL\DATA\TelestrationGame_log.ldf' , SIZE = 1040KB , MAXSIZE = 2048GB , FILEGROWTH = 10%)
GO
ALTER DATABASE [TelestrationGame] SET COMPATIBILITY_LEVEL = 110
GO
IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [TelestrationGame].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO
ALTER DATABASE [TelestrationGame] SET ANSI_NULL_DEFAULT OFF 
GO
ALTER DATABASE [TelestrationGame] SET ANSI_NULLS OFF 
GO
ALTER DATABASE [TelestrationGame] SET ANSI_PADDING OFF 
GO
ALTER DATABASE [TelestrationGame] SET ANSI_WARNINGS OFF 
GO
ALTER DATABASE [TelestrationGame] SET ARITHABORT OFF 
GO
ALTER DATABASE [TelestrationGame] SET AUTO_CLOSE ON 
GO
ALTER DATABASE [TelestrationGame] SET AUTO_SHRINK OFF 
GO
ALTER DATABASE [TelestrationGame] SET AUTO_UPDATE_STATISTICS ON 
GO
ALTER DATABASE [TelestrationGame] SET CURSOR_CLOSE_ON_COMMIT OFF 
GO
ALTER DATABASE [TelestrationGame] SET CURSOR_DEFAULT  GLOBAL 
GO
ALTER DATABASE [TelestrationGame] SET CONCAT_NULL_YIELDS_NULL OFF 
GO
ALTER DATABASE [TelestrationGame] SET NUMERIC_ROUNDABORT OFF 
GO
ALTER DATABASE [TelestrationGame] SET QUOTED_IDENTIFIER OFF 
GO
ALTER DATABASE [TelestrationGame] SET RECURSIVE_TRIGGERS OFF 
GO
ALTER DATABASE [TelestrationGame] SET  ENABLE_BROKER 
GO
ALTER DATABASE [TelestrationGame] SET AUTO_UPDATE_STATISTICS_ASYNC OFF 
GO
ALTER DATABASE [TelestrationGame] SET DATE_CORRELATION_OPTIMIZATION OFF 
GO
ALTER DATABASE [TelestrationGame] SET TRUSTWORTHY OFF 
GO
ALTER DATABASE [TelestrationGame] SET ALLOW_SNAPSHOT_ISOLATION OFF 
GO
ALTER DATABASE [TelestrationGame] SET PARAMETERIZATION SIMPLE 
GO
ALTER DATABASE [TelestrationGame] SET READ_COMMITTED_SNAPSHOT OFF 
GO
ALTER DATABASE [TelestrationGame] SET HONOR_BROKER_PRIORITY OFF 
GO
ALTER DATABASE [TelestrationGame] SET RECOVERY SIMPLE 
GO
ALTER DATABASE [TelestrationGame] SET  MULTI_USER 
GO
ALTER DATABASE [TelestrationGame] SET PAGE_VERIFY CHECKSUM  
GO
ALTER DATABASE [TelestrationGame] SET DB_CHAINING OFF 
GO
ALTER DATABASE [TelestrationGame] SET FILESTREAM( NON_TRANSACTED_ACCESS = OFF ) 
GO
ALTER DATABASE [TelestrationGame] SET TARGET_RECOVERY_TIME = 0 SECONDS 
GO
USE [TelestrationGame]
GO
/****** Object:  Table [dbo].[current_game]    Script Date: 2025-07-18 오후 5:48:12 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[current_game](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[topics] [nvarchar](50) NULL,
	[current_turn] [int] NULL,
	[game_status] [varchar](20) NULL,
	[final_answer] [nvarchar](50) NULL,
	[is_correct] [bit] NULL,
 CONSTRAINT [PK_current_game] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[game_rounds]    Script Date: 2025-07-18 오후 5:48:12 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[game_rounds](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[round_number] [int] NOT NULL,
	[player_number] [int] NOT NULL,
	[drawing_data] [nvarchar](max) NULL,
	[created_at] [datetime] NULL,
 CONSTRAINT [PK_game_rounds] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO
/****** Object:  Table [dbo].[players]    Script Date: 2025-07-18 오후 5:48:12 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[players](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[player_number] [int] NOT NULL,
	[name] [nvarchar](20) NULL,
	[logined] [tinyint] NULL,
 CONSTRAINT [PK_players] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[topics]    Script Date: 2025-07-18 오후 5:48:12 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[topics](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[text] [nvarchar](50) NULL,
	[difficulty] [nvarchar](10) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET IDENTITY_INSERT [dbo].[current_game] ON 

INSERT [dbo].[current_game] ([id], [topics], [current_turn], [game_status], [final_answer], [is_correct]) VALUES (25, N'스마트폰', 1, N'playing', NULL, NULL)
SET IDENTITY_INSERT [dbo].[current_game] OFF
SET IDENTITY_INSERT [dbo].[players] ON 

INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (30, 1, N'1', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (31, 2, N'2', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (32, 3, N'3', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (33, 1, N'1', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (34, 2, N'2', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (35, 3, N'3', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (36, 1, N'1', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (37, 2, N'2', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (38, 3, N'3', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (39, 1, N'1', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (40, 2, N'2', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (41, 3, N'3', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (42, 1, N'1', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (43, 2, N'2', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (44, 3, N'3', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (45, 1, N'1', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (46, 2, N'2', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (47, 3, N'3', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (48, 1, N'1', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (49, 2, N'2', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (50, 3, N'3', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (51, 1, N'1', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (52, 2, N'2', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (53, 3, N'3', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (54, 1, N'1', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (55, 2, N'2', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (56, 3, N'3', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (57, 1, N'1', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (58, 2, N'2', 1)
INSERT [dbo].[players] ([id], [player_number], [name], [logined]) VALUES (59, 3, N'3', 1)
SET IDENTITY_INSERT [dbo].[players] OFF
SET IDENTITY_INSERT [dbo].[topics] ON 

INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (1, N'사과', N'easy')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (2, N'자동차', N'easy')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (3, N'고양이', N'easy')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (4, N'집', N'easy')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (5, N'나무', N'easy')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (6, N'햄버거', N'medium')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (7, N'컴퓨터', N'medium')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (8, N'비행기', N'medium')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (9, N'책', N'medium')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (10, N'시계', N'medium')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (11, N'스마트폰', N'hard')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (12, N'로봇', N'hard')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (13, N'우주선', N'hard')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (14, N'드래곤', N'hard')
INSERT [dbo].[topics] ([id], [text], [difficulty]) VALUES (15, N'마법사', N'hard')
SET IDENTITY_INSERT [dbo].[topics] OFF
ALTER TABLE [dbo].[current_game] ADD  DEFAULT ((1)) FOR [current_turn]
GO
ALTER TABLE [dbo].[current_game] ADD  DEFAULT ('playing') FOR [game_status]
GO
ALTER TABLE [dbo].[game_rounds] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[topics] ADD  DEFAULT ('medium') FOR [difficulty]
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'현재 턴 플레이어 번호' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'current_game', @level2type=N'COLUMN',@level2name=N'current_turn'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'게임 상태 (playing, completed)' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'current_game', @level2type=N'COLUMN',@level2name=N'game_status'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'최종 답안' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'current_game', @level2type=N'COLUMN',@level2name=N'final_answer'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'정답 여부' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'current_game', @level2type=N'COLUMN',@level2name=N'is_correct'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'라운드 번호' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'game_rounds', @level2type=N'COLUMN',@level2name=N'round_number'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'그림 데이터 (base64)' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'game_rounds', @level2type=N'COLUMN',@level2name=N'drawing_data'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'플레이어 순번' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'players', @level2type=N'COLUMN',@level2name=N'player_number'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'플레이어 이름' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'players', @level2type=N'COLUMN',@level2name=N'name'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'접속상태. 1=게임중 / 2=게임종료' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'players', @level2type=N'COLUMN',@level2name=N'logined'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'제시어' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'topics', @level2type=N'COLUMN',@level2name=N'text'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'난이도' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'topics', @level2type=N'COLUMN',@level2name=N'difficulty'
GO
USE [master]
GO
ALTER DATABASE [TelestrationGame] SET  READ_WRITE 
GO
