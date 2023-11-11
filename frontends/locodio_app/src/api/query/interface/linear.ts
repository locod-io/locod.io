export interface Roadmap {
  id:string;
  name:string;
  projects: Array<Project>;
}

export interface Project {
  id: string;
  name: string;
  url: string;
  color: string;
  description:string;
  startDate:string;
  targetDate:string;
  state:string;
  lead:string;
  teams: TeamCollection;
}

export interface TeamCollection {
  collection: Array<Team>;
}

export interface Team {
  id: string;
  name: string;
  color: string;
  url: string;
  labels: LabelCollection;
}

export interface LabelCollection {
  collection: Array<Label>;
}

export interface Label {
  id: string;
  name: string;
  url: string;
  color: string;
}

export interface Document {
  id: string;
  title: string;
  projectId: string;
}

export interface CacheIssue {
  id: string;
  identifier: string;
  title: string;
}

export interface Issue {
  id: string;
  identifier: string;
  title: string;
  state: State;
  description: string;
  assigneeId: string;
  assigneeName: string;
  createdAt: string;
  completedAt: string;
  archivedAt: string;
  subIssues: IssueCollection;
  comment: CommentCollection;
}

export interface State {
  id: string;
  name: string;
  type: string;
  color: string;
  position: string;
}

export interface IssueCollection {
  collection: Array<Issue>;
}

export interface CommentCollection {
  collection: Array<Comment>;
}

export interface Comment {
  id: string;
  body: string;
  createdAt: string;
  url: string;
  userId: string;
  userName: string;
  parentId: string;
  replies: CommentCollection;
}